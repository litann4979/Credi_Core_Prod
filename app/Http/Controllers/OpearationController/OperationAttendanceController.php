<?php

namespace App\Http\Controllers\OpearationController;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OperationAttendanceController extends Controller
{
    /**
     * Show all attendance records for the logged-in operation user.
     */
 public function index()
    {
        $user = Auth::user();
        $attendanceRecords = Attendance::where('operation_id', $user->id)
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

        return view('Opearation.Attendance.index', compact('attendanceRecords'));
    }

    public function show($id)
    {
        $attendance = Attendance::where('id', $id)
            ->where('operation_id', Auth::user()->id)
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
        $existing = Attendance::where('operation_id', $user->id)
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
            'operation_id' => $user->id,
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

        $attendance = Attendance::where('operation_id', $user->id)
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

    // public function submitComplaint(Request $request)
    // {
    //     $request->validate([
    //         'attendance_id' => 'required|exists:attendances,id',
    //         'complaint_type' => 'required|in:wrong-time,location-issue,system-error,missed-punch,other',
    //         'description' => 'required|string',
    //         'priority' => 'required|in:low,medium,high,urgent',
    //     ]);

    //     $attendance = Attendance::where('id', $request->attendance_id)
    //         ->where('operation_id', Auth::user()->id)
    //         ->firstOrFail();

    //     $complaint = Complaint::create([
    //         'attendance_id' => $attendance->id,
    //         'operation_id' => Auth::user()->id,
    //         'complaint_type' => $request->complaint_type,
    //         'description' => $request->description,
    //         'priority' => $request->priority,
    //         'status' => 'pending',
    //     ]);

    //     return response()->json([
    //         'message' => 'Complaint submitted successfully',
    //         'complaint' => [
    //             'id' => $complaint->id,
    //             'attendance_id' => $complaint->attendance_id,
    //             'complaint_type' => $complaint->complaint_type,
    //             'description' => $complaint->description,
    //             'priority' => $complaint->priority,
    //             'status' => $complaint->status,
    //         ]
    //     ]);
    // }

    private function calculateTotalHours(Carbon $checkIn, Carbon $checkOut)
    {
        $diffInMinutes = $checkOut->diffInMinutes($checkIn);
        // Subtract default break time (60 minutes)
        $diffInMinutes = max(0, $diffInMinutes - 60);
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;
        return sprintf('%dh %dm', $hours, $minutes);
    }

    private function determineStatus(Attendance $attendance)
    {
        if (!$attendance->check_in) {
            return 'absent';
        }

        $checkInTime = Carbon::parse($attendance->check_in);
        $expectedCheckIn = Carbon::parse($attendance->date)->setTime(9, 0); // Assuming 9 AM is standard check-in

        if ($checkInTime->greaterThan($expectedCheckIn->addMinutes(30))) {
            return 'late';
        }

        if ($attendance->check_out) {
            $checkOutTime = Carbon::parse($attendance->check_out);
            $hoursWorked = $checkOutTime->diffInHours($checkInTime);
            return $hoursWorked < 4 ? 'half-day' : 'present';
        }

        return 'present';
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
