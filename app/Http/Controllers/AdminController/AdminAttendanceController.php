<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
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
    // Fetch existing attendance records
    $existingRecords = $query->orderBy('date', 'desc')->get();
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
    // Pass data to view
    return view('admin.attendance.index', compact('employees', 'teamLeads', 'operations', 'attendanceRecords', 'range', 'startDate', 'endDate', 'attendanceRoleFilter', 'filteredUserId', 'selectedUserType'));
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
}
