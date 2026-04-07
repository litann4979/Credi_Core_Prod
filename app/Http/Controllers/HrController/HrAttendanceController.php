<?php

namespace App\Http\Controllers\HrController;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HrAttendanceController extends Controller
{
  public function index(Request $request)
{
    // Fetch users for filter dropdowns
    $allUsers = User::select('id', 'name', 'designation')->get();
    $employees = $allUsers->where('designation', 'employee');
    $teamLeads = $allUsers->where('designation', 'team_lead');
    $operations = $allUsers->whereIn('designation', ['operation', 'operations']);

    // Build attendance query
    $query = Attendance::query()
        ->with(['employee:id,name,designation', 'teamLead:id,name,designation', 'operation:id,name,designation']);

    // Apply date range filter
    $range = $request->input('range', 'today');
    $today = Carbon::today(); // 2025-07-31
    $startDate = $request->input('start_date', $today->toDateString());
    $endDate = $request->input('end_date', $today->toDateString());

    if ($range === 'custom' && $request->has('start_date') && $request->has('end_date')) {
        try {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
            if ($startDate->gt($endDate)) {
                [$startDate, $endDate] = [$endDate, $startDate];
            }
            $query->whereBetween('date', [$startDate, $endDate]);
        } catch (\Exception $e) {
            Log::warning('Invalid custom date range: ' . $e->getMessage());
            $query->whereDate('date', $today);
        }
    } elseif ($range === '30_days') {
        $startDate = $today->copy()->subDays(30);
        $endDate = $today;
        $query->whereBetween('date', [$startDate, $endDate]);
    } elseif ($range === '15_days') {
        $startDate = $today->copy()->subDays(15);
        $endDate = $today;
        $query->whereBetween('date', [$startDate, $endDate]);
    } elseif ($range === '7_days') {
        $startDate = $today->copy()->subDays(7);
        $endDate = $today;
        $query->whereBetween('date', [$startDate, $endDate]);
    } else {
        $startDate = $today;
        $endDate = $today;
        $query->whereDate('date', $today);
    }

    // Apply role filter
    $attendanceRoleFilter = $request->input('attendance_role_filter', 'all');
    $query->where(function ($q) use ($attendanceRoleFilter) {
        if ($attendanceRoleFilter === 'employee') {
            $q->whereHas('employee', function ($subQ) {
                $subQ->where('designation', 'employee');
            });
        } elseif ($attendanceRoleFilter === 'team_lead') {
            $q->whereHas('teamLead', function ($subQ) {
                $subQ->where('designation', 'team_lead');
            });
        } elseif ($attendanceRoleFilter === 'operation') {
            $q->whereHas('operation', function ($subQ) {
                $subQ->whereIn('designation', ['operation', 'operations']);
            });
        } else {
            $q->where(function ($subQ) {
                $subQ->whereHas('employee', function ($e) {
                    $e->whereIn('designation', ['employee', 'team_lead', 'operation', 'operations']);
                })->orWhereHas('teamLead', function ($t) {
                    $t->where('designation', 'team_lead');
                })->orWhereHas('operation', function ($o) {
                    $o->whereIn('designation', ['operation', 'operations']);
                });
            });
        }
    });

    // Apply individual user filter only if explicitly provided
    $filteredUserId = null;
    $selectedUserType = null;
    if ($request->has('employee') && $employeeId = $request->input('employee')) {
        $query->where('employee_id', $employeeId);
        $filteredUserId = $employeeId;
        $selectedUserType = 'employee';
    } elseif ($request->has('team_lead') && $teamLeadId = $request->input('team_lead')) {
        $query->where('team_lead_id', $teamLeadId);
        $filteredUserId = $teamLeadId;
        $selectedUserType = 'team_lead';
    } elseif ($request->has('operation') && $operationId = $request->input('operation')) {
        $query->where('operation_id', $operationId);
        $filteredUserId = $operationId;
        $selectedUserType = 'operation';
    }

    // Log query for debugging
    Log::info('Attendance Query: ' . $query->toSql(), $query->getBindings());

    // Fetch existing attendance records
    $existingRecords = $query->orderBy('date', 'desc')->get();
    Log::info('Existing Records', $existingRecords->toArray());

    // Generate all dates in the range
    $allDates = [];
    $currentDate = $startDate->copy();
    while ($currentDate->lte($endDate)) {
        $allDates[] = $currentDate->copy();
        $currentDate->addDay();
    }

    // Map existing attendance records with unique keys
    $attendanceMap = $existingRecords->mapWithKeys(function ($attendance) {
        $key = $attendance->date->toDateString() . '_' . ($attendance->employee_id ?? $attendance->team_lead_id ?? $attendance->operation_id);
        return [$key => $attendance];
    });
    Log::info('Attendance Map', $attendanceMap->toArray());

    // Generate attendance records including all users
    $attendanceRecords = collect($allDates)->flatMap(function ($date) use ($attendanceMap, $filteredUserId, $allUsers, $attendanceRoleFilter) {
        $dateStr = $date->toDateString();
        $records = [];

        // Get relevant users based on role filter
        $relevantUsers = $filteredUserId ? $allUsers->where('id', $filteredUserId) : $allUsers->whereIn('designation', $this->getDesignationsForRole($attendanceRoleFilter));

        foreach ($relevantUsers as $user) {
            $key = $dateStr . '_' . $user->id;
            $attendance = $attendanceMap->get($key);

            if ($attendance) {
                $checkIn = $attendance->check_in ? Carbon::parse($attendance->check_in) : null;
                $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out) : null;
                $totalHours = $checkIn && $checkOut ? $this->calculateTotalHours($checkIn, $checkOut) : '0h 0m';
                $employeeName = $attendance->employee?->name ?? $attendance->teamLead?->name ?? $attendance->operation?->name ?? 'N/A';
                $employeeRole = $attendance->employee?->designation ?? $attendance->teamLead?->designation ?? $attendance->operation?->designation ?? 'N/A';
            } else {
                $employeeName = $user->name ?? 'N/A';
                $employeeRole = $user->designation ?? 'N/A';
                $checkIn = null;
                $checkOut = null;
                $totalHours = '0h 0m';
            }

            Log::info('Attendance Summary', [
                'date' => $dateStr,
                'employee_name' => $employeeName,
                'employee_role' => $employeeRole,
                'total_hours' => $totalHours,
            ]);

            $records[] = [
                'id' => $attendance?->id ?? null,
                'date' => $dateStr,
                'day' => $date->format('l'),
                'status' => $attendance ? $this->determineStatus($attendance) : 'absent',
                'check_in' => $checkIn ? $checkIn->toDateTimeString() : null,
                'check_out' => $checkOut ? $checkOut->toDateTimeString() : null,
                'check_in_location' => $attendance?->check_in_location ?? null,
                'check_out_location' => $attendance?->check_out_location ?? null,
                'check_in_coordinates' => $attendance?->check_in_coordinates ?? null,
                'check_out_coordinates' => $attendance?->check_out_coordinates ?? null,
                'notes' => $attendance?->notes ?? null,
                'checkin_image' => $attendance?->checkin_image ?? null,
                'checkout_image' => $attendance?->checkout_image ?? null,
                'total_hours' => $totalHours,
                'break_time' => '1h 0m',
                'employee_name' => $employeeName,
                'employee_role' => $employeeRole,
            ];
        }

        return $records;
    });

    // Log record count
    Log::info('Attendance Records Count: ' . $attendanceRecords->count());

    // Pass data to view
    return view('hr.attendance.index', compact('employees', 'teamLeads', 'operations', 'attendanceRecords', 'range', 'startDate', 'endDate', 'attendanceRoleFilter', 'filteredUserId', 'selectedUserType'));
}

private function getDesignationsForRole($role)
{
    return match ($role) {
        'employee' => ['employee'],
        'team_lead' => ['team_lead'],
        'operation' => ['operation', 'operations'],
        'all' => ['employee', 'team_lead', 'operation', 'operations'],
        default => ['employee', 'team_lead', 'operation', 'operations'],
    };
}

    /**
     * Calculate total hours worked, subtracting break time.
     *
     * @param \Carbon\Carbon $checkIn
     * @param \Carbon\Carbon $checkOut
     * @return string
     */
    private function calculateTotalHours(Carbon $checkIn, Carbon $checkOut)
   {
       $diffInMinutes = $checkIn->diffInMinutes($checkOut);

        $diffInMinutes = max(0, $diffInMinutes - 60);
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;
        return sprintf('%dh %dm', $hours, $minutes);
    }

    /**
     * Determine attendance status based on check-in and check-out.
     *
     * @param \App\Models\Attendance $attendance
     * @return string
     */
    private function determineStatus(Attendance $attendance)
    {
        if (!$attendance->check_in) {
            return 'absent';
        }

        $checkInTime = Carbon::parse($attendance->check_in);
        $expectedCheckIn = Carbon::parse($attendance->date)->setTime(9, 0);

        if ($checkInTime->greaterThan($expectedCheckIn->addMinutes(40))) {
            return 'late';
        }

        if ($attendance->check_out) {
            $checkOutTime = Carbon::parse($attendance->check_out);
            $hoursWorked = $checkOutTime->diffInHours($checkInTime);
            return $hoursWorked < 4 ? 'half-day' : 'present';
        }

        return 'present';
    }


     public function fetchAttendance()
    {
        $user = Auth::user();
        $attendanceRecords = Attendance::where('hr_id', $user->id)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($attendance) {
                $checkIn = $attendance->check_in ? Carbon::parse($attendance->check_in) : null;
                $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out) : null;
                $totalHours = $checkIn && $checkOut ? $this->calculateTotalHours($checkIn, $checkOut) : '0h 0m';

                return [
                    'id' => $attendance->id,
                    'date' => $attendance->date,
                    'day' => Carbon::parse($attendance->date)->format('l'),
                    'status' => $this->determineStatus($attendance),
                    'check_in' => $checkIn ? $checkIn->toDateTimeString() : null,
                    'check_out' => $checkOut ? $checkOut->toDateTimeString() : null,
                    'check_in_location' => $attendance->check_in_location,
                    'check_out_location' => $attendance->check_out_location,
                    'check_in_coordinates' => $attendance->check_in_coordinates,
                    'check_out_coordinates' => $attendance->check_out_coordinates,
                    'notes' => $attendance->notes,
                    'checkin_image' => $attendance->checkin_image,
                    'checkout_image' => $attendance->checkout_image,
                    'total_hours' => $totalHours,
                    'break_time' => '1h 0m', // Placeholder, adjust based on actual break time logic
                ];
            });

        return view('hr.attendance.attendance', compact('attendanceRecords'));
    }

    public function show($id)
    {
        $attendance = Attendance::where('id', $id)
            ->where('hr_id', Auth::user()->id)
            ->firstOrFail();

        $checkIn = $attendance->check_in ? Carbon::parse($attendance->check_in) : null;
        $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out) : null;

        return response()->json([
            'id' => $attendance->id,
            'date' => $attendance->date,
            'status' => $this->determineStatus($attendance),
            'check_in' => $checkIn ? $checkIn->toDateTimeString() : null,
            'check_out' => $checkOut ? $checkOut->toDateTimeString() : null,
            'check_in_location' => $attendance->check_in_location,
            'check_out_location' => $attendance->check_out_location,
            'check_in_coordinates' => $attendance->check_in_coordinates,
            'check_out_coordinates' => $attendance->check_out_coordinates,
            'notes' => $attendance->notes,
            'checkin_image' => $attendance->checkin_image,
            'checkout_image' => $attendance->checkout_image,
            'total_hours' => $checkIn && $checkOut ? $this->calculateTotalHours($checkIn, $checkOut) : '0h 0m',
            'break_time' => '1h 0m', // Placeholder, adjust based on actual break time logic
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Check if already checked in today
        $existing = Attendance::where('hr_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            return response()->json(['error' => 'Already checked in for today'], 400);
        }

        // Store photo
        $photoPath = $this->storeImage($request->photo, 'checkin');


// Debug image storage
$fullPath = str_replace('/storage', 'public', $photoPath); // Convert URL path to storage path
if (Storage::exists($fullPath)) {
    Log::info("✅ Image exists at: {$fullPath}");
} else {
    Log::warning("❌ Image NOT found at: {$fullPath}");
    dd("Image not found at: $fullPath");
}


        $attendance = Attendance::create([
            'hr_id' => $user->id,
            'date' => $today,
            'check_in' => Carbon::now(),
            'check_in_location' => $request->input('location', 'Office Building, Mumbai'),
            'check_in_coordinates' => "{$request->latitude},{$request->longitude}",
            'notes' => $request->notes,
            'checkin_image' => $photoPath,
            'is_within_geofence' => $this->checkGeofence($request->latitude, $request->longitude),
        ]);

        return response()->json([
            'message' => 'Check-in recorded successfully',
            'attendance' => [
                'id' => $attendance->id,
                'date' => $attendance->date,
                'status' => $this->determineStatus($attendance),
                'check_in' => $attendance->check_in->toDateTimeString(),
                'check_out' => null,
                'check_in_location' => $attendance->check_in_location,
                'check_out_location' => null,
                'check_in_coordinates' => $attendance->check_in_coordinates,
                'check_out_coordinates' => null,
                'notes' => $attendance->notes,
                'checkin_image' => $attendance->checkin_image,
                'checkout_image' => null,
                'total_hours' => '0h 0m',
            ]
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('hr_id', $user->id)
            ->where('date', $today)
            ->firstOrFail();

        if ($attendance->check_out) {
            return response()->json(['error' => 'Already checked out for today'], 400);
        }

        $photoPath = $this->storeImage($request->photo, 'checkout');

        $attendance->update([
            'check_out' => Carbon::now(),
            'check_out_location' => $request->input('location', 'Office Building, Mumbai'),
            'check_out_coordinates' => "{$request->latitude},{$request->longitude}",
            'notes' => $request->notes ? ($attendance->notes ? $attendance->notes . "\n" . $request->notes : $request->notes) : $attendance->notes,
            'checkout_image' => $photoPath,
            'is_within_geofence' => $this->checkGeofence($request->latitude, $request->longitude),
        ]);

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::parse($attendance->check_out);

        return response()->json([
            'message' => 'Check-out recorded successfully',
            'attendance' => [
                'id' => $attendance->id,
                'date' => $attendance->date,
                'status' => $this->determineStatus($attendance),
                'check_in' => $checkIn->toDateTimeString(),
                'check_out' => $checkOut->toDateTimeString(),
                'check_in_location' => $attendance->check_in_location,
                'check_out_location' => $attendance->check_out_location,
                'check_in_coordinates' => $attendance->check_in_coordinates,
                'check_out_coordinates' => $attendance->check_out_coordinates,
                'notes' => $attendance->notes,
                'checkin_image' => $attendance->checkin_image,
                'checkout_image' => $attendance->checkout_image,
                'total_hours' => $this->calculateTotalHours($checkIn, $checkOut),
            ]
        ]);
    }





    private function storeImage($base64Image, $folder)
    {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        $filename = 'attendance_' . time() . '.jpg';
        $path = "public/{$folder}/{$filename}";
        Storage::put($path, $imageData);
        return Storage::url($path);
    }

    private function checkGeofence($latitude, $longitude)
    {
        // Example geofence check (assuming office location at Mumbai)
        $officeLat = 19.0760; // Example Mumbai coordinates
        $officeLng = 72.8777;
        $maxDistance = 0.5; // 500 meters radius

        $distance = $this->calculateDistance($latitude, $longitude, $officeLat, $officeLng);
        return $distance <= $maxDistance;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c; // Distance in km
    }
}
