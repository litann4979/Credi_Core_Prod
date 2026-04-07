<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\GeofenceSettings;
use App\Models\Leave;
use App\Models\LocationLog;
use App\Models\OfficeRule;
use App\Models\Score;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    private const EARTH_RADIUS_METERS = 6371000;

    private function getOfficeRule(): OfficeRule
    {
        return OfficeRule::query()->latest('id')->firstOrFail();
    }

    private function getOrCreateDailyScore(int $userId, Carbon $date): Score
    {
        return Score::firstOrCreate(
            [
                'user_id' => $userId,
                'date' => $date->toDateString(),
            ],
            [
                'total_score' => 0,
                'target_score' => 0,
                'lead_score' => 0,
                'attendance_score' => 0,
                'leave_score' => 0,
                'additional_target_score' => 0,
                'additional_lead_score' => 0,
                'late_penalty' => 0,
                'late_15min_penalty' => 0,
                'early_checkout_penalty' => 0,
                'break_penalty' => 0,
                'lunch_penalty' => 0,
                'geofence_penalty' => 0,
                'work_penalty' => 0,
            ]
        );
    }

    private function recalculateTotalScore(Score $score): void
    {
        $earned =
            (float) $score->target_score +
            (float) $score->lead_score +
            (float) $score->attendance_score +
            (float) $score->leave_score +
            (float) $score->additional_target_score +
            (float) $score->additional_lead_score;

        $penalties =
            (float) $score->late_penalty +
            (float) $score->late_15min_penalty +
            (float) $score->early_checkout_penalty +
            (float) $score->break_penalty +
            (float) $score->lunch_penalty +
            (float) $score->geofence_penalty +
            (float) $score->work_penalty;

        $score->total_score = $earned - $penalties;
    }

    private function calculateDistanceMeters(
        float $fromLat,
        float $fromLng,
        float $toLat,
        float $toLng
    ): float {
        $lat1 = deg2rad($fromLat);
        $lon1 = deg2rad($fromLng);
        $lat2 = deg2rad($toLat);
        $lon2 = deg2rad($toLng);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }

    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $geofence = GeofenceSettings::query()->latest('id')->first();
        if (! $geofence) {
            return response()->json([
                'status' => 'error',
                'message' => 'Office geofence not configured.',
            ], 404);
        }

        $latitude = (float) $request->input('latitude');
        $longitude = (float) $request->input('longitude');

        $distance = $this->calculateDistanceMeters(
            $latitude,
            $longitude,
            (float) $geofence->latitude,
            (float) $geofence->longitude
        );

        $isOutside = $distance > (float) $geofence->radius;

        try {
            LocationLog::create([
                'employee_id' => $user->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'distance_from_office' => round($distance, 2),
                'is_outside' => $isOutside,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store location log.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Live location updated successfully.',
            'distance' => round($distance, 2),
            'is_outside' => $isOutside,
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Attendance::with('employee')
            ->where('employee_id', $user->id);

        // Apply filters
        if ($request->has('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->has('status')) {
            if ($request->status === 'present') {
                $query->whereNotNull('check_in');
            } elseif ($request->status === 'absent') {
                $query->whereNull('check_in');
            }
        }

        if ($request->has(['from_date', 'to_date'])) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        $records = $query->orderBy('date', 'desc')->get();

        // Summary
        $totalDays = $records->count();
        $present = $records->whereNotNull('check_in')->count();
        $absent = $totalDays - $present;

        $today = $records->where('date', today()->toDateString())->first();
        $checkIn = optional($today)->check_in;
        $checkOut = optional($today)->check_out;

        // Calculate total working hours for today
        $workingHours = null;
        if ($checkIn && $today && $today->sessions) {
            $totalMinutes = 0;
            $sessions = is_string($today->sessions) ? json_decode($today->sessions, true) : $today->sessions;
            $sessions = is_array($sessions) ? $sessions : [];
            foreach ($sessions as $session) {
                if (!empty($session['check_in']) && !empty($session['check_out'])) {
                    $totalMinutes += Carbon::parse($session['check_in'])->diffInMinutes($session['check_out']);
                }
            }
            $workingHours = $totalMinutes / 60;
        }

        return response()->json([
            'status' => 'success',
            'summary' => [
                'total_days' => $totalDays,
                'present' => $present,
                'absent' => $absent,
                'today_check_in' => $checkIn,
                'today_check_out' => $checkOut,
                'working_hours_today' => $workingHours ? round($workingHours, 2) : null
            ],
            'records' => $records->map(function ($record) {
                $sessions = is_string($record->sessions) ? json_decode($record->sessions, true) : $record->sessions;
                $sessions = is_array($sessions) ? $sessions : [];
                $totalWorkedHours = 0;
                foreach ($sessions as $session) {
                    if (!empty($session['check_in']) && !empty($session['check_out'])) {
                        $totalWorkedHours += Carbon::parse($session['check_in'])->diffInMinutes($session['check_out']) / 60;
                    }
                }
                return [
                    'employee_name' => $record->employee->name,
                    'date' => $record->date,
                    'check_in' => $record->check_in,
                    'check_out' => $record->check_out,
                    'check_in_location' => $record->check_in_location,
                    'check_out_location' => $record->check_out_location,
                    'check_in_coordinates' => $record->check_in_coordinates,
                    'check_out_coordinates' => $record->check_out_coordinates,
                    'notes' => $record->notes,
                    'checkin_image' => $record->checkin_image,
                    'checkout_image' => $record->checkout_image,
                    'reason' => $record->reason,
                    'worked_hours' => round($totalWorkedHours, 2),
                    'sessions' => $sessions
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = now();



        // Check if today is Sunday
        if ($today->isSunday()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance is not allowed on Sunday.',
                'button_action' => 'none'
            ], 403);
        }

        // Check for approved leave
        $leaveToday = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if ($leaveToday) {

            return response()->json([
                'status' => 'error',
                'message' => 'Attendance not allowed. You are on approved leave today.',
                'button_action' => 'none'
            ], 403);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'checkin_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'check_in_location' => 'nullable|string|max:255',
            'check_in_coordinates' => 'required|string|regex:/^[-]?[0-9]{1,3}\.[0-9]{6},[-]?[0-9]{1,3}\.[0-9]{6}$/',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'button_action' => 'checkin'
            ], 422);
        }

        // Prepare session data
        $sessionData = [
            'check_in' => now()->toDateTimeString(),
            'check_in_location' => $request->check_in_location,
            'check_in_coordinates' => $request->check_in_coordinates,
            'check_out' => null,
            'check_out_location' => null,
            'check_out_coordinates' => null
        ];
        $encodedSessions = json_encode([$sessionData]);
        if (json_last_error() !== JSON_ERROR_NONE) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process check-in due to data encoding error.',
                'button_action' => 'checkin'
            ], 500);
        }



        // Find or create today's attendance record
        $attendance = Attendance::where('employee_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance && $attendance->check_in && !$attendance->check_out) {

            return response()->json([
                'status' => 'error',
                'message' => 'You are already checked in. Please check out before checking in again.',
                'button_action' => 'checkout',
                'attendance_id' => $attendance->id
            ], 403);
        }

        DB::beginTransaction();
        try {
            if (!$attendance) {
                // First check-in of the day
                $officeRule = $this->getOfficeRule();
                $officeStart = Carbon::parse($today->toDateString() . ' ' . $officeRule->office_start_time);
                $lateMinutes = $now->greaterThan($officeStart) ? $officeStart->diffInMinutes($now) : 0;

                $attributes = [
                    'employee_id' => $user->id,
                    'date' => $today,
                    'check_in' => $now,
                    'check_in_location' => $request->check_in_location,
                    'check_in_coordinates' => $request->check_in_coordinates,
                    'checkin_image' => $request->hasFile('checkin_image')
                        ? '/storage/' . $request->file('checkin_image')->store('attendance/checkin', 'public')
                        : null,
                    'notes' => $request->notes,
                    'reason' => 'Initial check-in',
                    'sessions' => $encodedSessions,
                    'late_minutes' => $lateMinutes,
                ];

                $attendance = Attendance::create($attributes);

                // Idempotent penalty assignment for the day.
                $score = $this->getOrCreateDailyScore((int) $user->id, $today);
                if ($lateMinutes > 0) {
                    if ($lateMinutes < 15) {
                        $score->late_penalty = (float) ($officeRule->late_penalty ?? 0);
                        $score->late_15min_penalty = 0;
                    } else {
                        $score->late_penalty = 0;
                        $score->late_15min_penalty = (float) ($officeRule->late_15min_penalty ?? 0);
                    }
                } else {
                    $score->late_penalty = 0;
                    $score->late_15min_penalty = 0;
                }
                $this->recalculateTotalScore($score);
                $score->save();
            } else {
                // Re-check-in (e.g., after lunch break)
                $sessions = is_string($attendance->sessions) ? json_decode($attendance->sessions, true) : $attendance->sessions;
                if (!is_array($sessions)) {
                    $sessions = [];
                }
                $sessions[] = $sessionData;
                $encodedSessions = json_encode($sessions);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to process re-check-in due to data encoding error.',
                        'button_action' => 'checkin'
                    ], 500);
                }
                $attributes = [
                    'check_in' => $now,
                    'check_out' => null, // Reset check_out for re-check-in
                    'check_in_location' => $request->check_in_location,
                    'check_in_coordinates' => $request->check_in_coordinates,
                    'checkin_image' => $request->hasFile('checkin_image')
                        ? '/storage/' . $request->file('checkin_image')->store('attendance/checkin', 'public')
                        : $attendance->checkin_image,
                    'notes' => $request->notes ?? $attendance->notes,
                    'reason' => 'Re-check-in',
                    'sessions' => $encodedSessions
                ];
                $attendance->update($attributes);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to record check-in.',
                'button_action' => 'checkin'
            ], 500);
        }



        return response()->json([
            'status' => 'success',
            'message' => 'Check-in recorded successfully.',
            'button_action' => 'checkout',
            'data' => $attendance,
            'attendance_id' => $attendance->id
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $attendanceDate = Carbon::parse($attendance->date)->startOfDay();
        $now = now();

        // Validate request
        $validator = Validator::make($request->all(), [
            'check_out_location' => 'nullable|string|max:255',
            'check_out_coordinates' => 'required|string|regex:/^[-]?[0-9]{1,3}\.[0-9]{6},[-]?[0-9]{1,3}\.[0-9]{6}$/',
            'checkout_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'button_action' => 'checkout'
            ], 422);
        }

        // Update the latest session for check-out
        $sessions = is_string($attendance->sessions) ? json_decode($attendance->sessions, true) : $attendance->sessions;
        $sessions = is_array($sessions) ? $sessions : [];
        if (!empty($sessions)) {
            $latestSession = &$sessions[count($sessions) - 1];
            if (!empty($latestSession['check_out'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Already checked out for the current session.',
                    'button_action' => 'checkin'
                ], 403);
            }
            $latestSession['check_out'] = now()->toDateTimeString();
            $latestSession['check_out_location'] = $request->check_out_location;
            $latestSession['check_out_coordinates'] = $request->check_out_coordinates;
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No active session found to check out.',
                'button_action' => 'checkin'
            ], 403);
        }

        // Handle image upload
        $checkoutImagePath = $attendance->checkout_image;
        if ($request->hasFile('checkout_image')) {
            if ($attendance->checkout_image) {
                $oldPath = str_replace('/storage/', '', $attendance->checkout_image);
                Storage::disk('public')->delete($oldPath);
            }
            $storedPath = $request->file('checkout_image')->store('attendance/checkout', 'public');
            $checkoutImagePath = '/storage/' . $storedPath;
        }

        // Calculate total worked hours
        $totalWorkedHours = 0;
        foreach ($sessions as $session) {
            if (!empty($session['check_in']) && !empty($session['check_out'])) {
                $totalWorkedHours += Carbon::parse($session['check_in'])->diffInMinutes($session['check_out']) / 60;
            }
        }

        DB::beginTransaction();
        try {
            $officeRule = $this->getOfficeRule();
            $officeEnd = Carbon::parse($attendanceDate->toDateString() . ' ' . $officeRule->office_end_time);
            $earlyCheckoutMinutes = $now->lessThan($officeEnd) ? $now->diffInMinutes($officeEnd) : 0;

            // Update attendance record
            $attributes = [
                'check_out' => $now,
                'check_out_location' => $request->check_out_location,
                'check_out_coordinates' => $request->check_out_coordinates,
                'checkout_image' => $checkoutImagePath,
                'notes' => $request->notes ?? $attendance->notes,
                'reason' => 'Check-out',
                'sessions' => json_encode($sessions),
                'early_checkout_minutes' => $earlyCheckoutMinutes,
            ];
            $attendance->update($attributes);

            $score = $this->getOrCreateDailyScore((int) $attendance->employee_id, $attendanceDate);
            $score->early_checkout_penalty = $earlyCheckoutMinutes > 0
                ? (float) ($officeRule->early_checkout_penalty ?? 0)
                : 0;
            $this->recalculateTotalScore($score);
            $score->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to record check-out.',
                'button_action' => 'checkout'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Check-out recorded successfully.',
            'button_action' => 'checkin',
            'data' => $attendance,
            'worked_hours' => round($totalWorkedHours, 2),
        ]);
    }

    /**
 * Check today's attendance status
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function checkTodayStatus()
{
    $user = Auth::user();
    $today = Carbon::today()->toDateString();

 

    // Check for approved leave
    $leaveToday = Leave::where('user_id', $user->id)
        ->where('status', 'approved')
        ->whereDate('start_date', '<=', $today)
        ->whereDate('end_date', '>=', $today)
        ->first();

    if ($leaveToday) {

        return response()->json([
            'status' => 'error',
            'message' => 'You are on approved leave today.',
            'button_action' => 'none'
        ], 403);
    }

    // Check if today is Sunday
    if (Carbon::today()->isSunday()) {

        return response()->json([
            'status' => 'error',
            'message' => 'Attendance is not allowed on Sunday.',
            'button_action' => 'none'
        ], 403);
    }

    // Find today's attendance record
    $attendance = Attendance::where('employee_id', $user->id)
        ->whereDate('date', $today)
        ->first();

    if (!$attendance) {

        return response()->json([
            'status' => 'pending',
            'message' => 'You have not checked in yet today.',
            'button_action' => 'checkin'
        ]);
    }

    $sessions = is_string($attendance->sessions) ? json_decode($attendance->sessions, true) : $attendance->sessions;
    $sessions = is_array($sessions) ? $sessions : [];
    $totalWorkedHours = 0;

    // Calculate total worked hours
    if (!empty($sessions)) {
        foreach ($sessions as $session) {
            if (!empty($session['check_in']) && !empty($session['check_out'])) {
                $totalWorkedHours += Carbon::parse($session['check_in'])->diffInMinutes($session['check_out']) / 60;
            }
        }
    }

    // Get latest session
    $latestSession = !empty($sessions) ? end($sessions) : null;

    // Load employee relationship to match update method's response
    $attendance->load('employee');

    // Format attendance data to include all relevant fields
    $formattedAttendance = [
        'id' => $attendance->id,
        'employee_id' => $attendance->employee_id,
        'date' => $attendance->date,
        'check_in' => $attendance->check_in,
        'check_out' => $attendance->check_out,
        'check_in_location' => $attendance->check_in_location,
        'check_out_location' => $attendance->check_out_location,
        'check_in_coordinates' => $attendance->check_in_coordinates,
        'check_out_coordinates' => $attendance->check_out_coordinates,
        'notes' => $attendance->notes,
        'checkin_image' => $attendance->checkin_image,
        'checkout_image' => $attendance->checkout_image,
        'reason' => $attendance->reason,
        'sessions' => $sessions,
        'employee' => $attendance->employee ? [
            'id' => $attendance->employee->id,
            'name' => $attendance->employee->name,
            // Add other employee fields as needed
        ] : null,
    ];

    if ($attendance->check_in && !$attendance->check_out) {
        return response()->json([
            'status' => 'checkin_done',
            'message' => 'You have checked in but not checked out yet.',
            'button_action' => 'checkout',
            'check_in_time' => $attendance->check_in,
            'worked_hours' => round($totalWorkedHours, 2),
            'attendance_id' => $attendance->id,
            'session' => $latestSession,
            'data' => $formattedAttendance // Include full attendance data
        ]);
    }

    if ($latestSession && !empty($latestSession['check_in']) && !empty($latestSession['check_out'])) {
        return response()->json([
            'status' => 'completed',
            'message' => 'You have checked out. Re-check-in if you return to work.',
            'button_action' => 'checkin',
            'check_in_time' => $attendance->check_in,
            'check_out_time' => $latestSession['check_out'],
            'worked_hours' => round($totalWorkedHours, 2),
            'attendance_id' => $attendance->id,
            'session' => $latestSession,
            'data' => $formattedAttendance // Include full attendance data
        ]);
    }


    return response()->json([
        'status' => 'pending',
        'message' => 'Attendance record found but no valid sessions. Please check in again.',
        'button_action' => 'checkin',
        'attendance_id' => $attendance->id,
        'session' => $latestSession,
        'data' => $formattedAttendance // Include full attendance data
    ]);
}
}
