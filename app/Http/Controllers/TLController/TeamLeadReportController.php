<?php

namespace App\Http\Controllers\TLController;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class TeamLeadReportController extends Controller
{

public function export(Request $request, $type)
{
    $query = Lead::with('teamLead')->whereNull('deleted_at');

    // 📆 Handle time filters: last 7/15/30 days or custom range
    if (in_array($type, ['7', '15', '30'])) {
        $fromDate = Carbon::now()->subDays((int) $type)->startOfDay();
        $query->whereDate('created_at', '>=', $fromDate);
    } elseif ($type === 'custom') {
        $from = $request->input('from');
        $to = $request->input('to');
        if ($from && $to) {
            $query->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }
    } elseif ($type !== 'total') {
        // 🎯 Status-based filtering (except 'total')
        if ($type === 'personal') {
            $query->where('status', 'personal_lead');
        } else {
            $query->where('status', $type);
        }
    }

    $leads = $query->get();

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename={$type}_leads_report.csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = ['Name', 'Company', 'Location', 'Amount', 'Success %', 'Expected Month', 'Status', 'Team Lead'];

    $callback = function () use ($leads, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($leads as $lead) {
            fputcsv($file, [
                $lead->client_name,
                $lead->company,
                $lead->city ?? $lead->district ?? $lead->state ?? '',
                $lead->lead_amount,
                $lead->success_percentage,
                optional($lead->expected_month)->format('F Y'),
                ucfirst($lead->status),
                optional($lead->teamLead)->name ?? '',
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function indexReports(Request $request)
{
    $today = Carbon::today();

    // ✅ Attendance - Only for today's check-in
    $attendances = Attendance::whereDate('check_in', $today)->get();

    // ✅ Stats for dashboard cards
    $stats = [
        'total_leads' => Lead::count(),
        'authorized_leads' => Lead::where('status', 'authorized')->count(),
        'login_leads' => Lead::where('status', 'login')->count(),
        'approved_leads' => Lead::where('status', 'approved')->count(),
        'disbursed_leads' => Lead::where('status', 'disbursed')->count(),
        'rejected_leads' => Lead::where('status', 'rejected')->count(),
        'active_employees' => User::where('designation', 'employee')->whereNull('deleted_at')->count(),
        'personal_leads' => Lead::where('status', 'personal_lead')->count(),
    ];

    // ✅ Lead Filtering Logic
    $query = Lead::query();
    $filter = $request->input('filter');
    $from = $request->input('from');
    $to = $request->input('to');

    if ($filter && $filter !== 'custom') {
        $days = (int)$filter;
        $fromDate = Carbon::now()->subDays($days)->startOfDay();
        $query->whereDate('created_at', '>=', $fromDate);
    }

    if ($filter === 'custom' && $from && $to) {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();
        $query->whereBetween('created_at', [$fromDate, $toDate]);
    }

    $leads = $query->with('employee')->latest()->get();

    $tasks = Task::with(['notifications' => function ($query) {
        $query->select('id', 'user_id', 'task_id');
    }])->get();

    // ✅ Add employees data with performance & attendance rate
$employees = User::where('designation', 'employee')
    ->whereNull('deleted_at')
    ->with(['attendances', 'leads'])
    ->get()
    ->map(function ($employee) {
        // ✅ Group attendance by date
        $groupedByDate = $employee->attendances->groupBy('date');

        // ✅ Count how many dates have both check-in and check-out (across any record)
        $presentDays = $groupedByDate->filter(function ($records) {
            $hasCheckIn = $records->firstWhere('check_in', '!=', null);
            $hasCheckOut = $records->firstWhere('check_out', '!=', null);
            return $hasCheckIn && $hasCheckOut;
        })->count();

        // ✅ Total working days (excluding Sundays)
        $joinDate = Carbon::parse($employee->created_at)->startOfDay();
        $today = now()->startOfDay();
        $totalWorkingDays = 0;

        for ($date = $joinDate->copy(); $date <= $today; $date->addDay()) {
            if (!$date->isSunday()) {
                $totalWorkingDays++;
            }
        }

        // ✅ Calculate attendance percentage
        $employee->attendance_rate = $totalWorkingDays > 0
            ? round(($presentDays / $totalWorkingDays) * 100)
            : 0;

        // ✅ Add performance rate (optional)
        $totalLeads = $employee->leads->count();
        $disbursedLeads = $employee->leads->where('status', 'disbursed')->count();
        $employee->performance_rate = $totalLeads > 0
            ? round(($disbursedLeads / $totalLeads) * 100)
            : 0;

        return $employee;
    });


    return view('TeamLead.reports.index', compact('stats', 'attendances', 'leads', 'tasks', 'employees'));
}



public function show($id)
{
    $user = User::with('teamLead', 'leads', 'attendances')->findOrFail($id);

    // Group attendance by date
    $groupedByDate = $user->attendances->groupBy('date');

    // Loop through each attendance date to classify
    $presentDays = 0;
    $halfDays = 0;
    $attendanceDates = [];

    foreach ($groupedByDate as $date => $records) {
        $hasCheckIn = $records->firstWhere('check_in', '!=', null);
        $hasCheckOut = $records->firstWhere('check_out', '!=', null);

        if ($hasCheckIn && $hasCheckOut) {
            $presentDays++;
        } elseif ($hasCheckIn && !$hasCheckOut) {
            $halfDays++;
        }

        $attendanceDates[] = $date;
    }

    // Total working days (exclude Sundays)
    $start = Carbon::parse($user->created_at)->startOfDay();
    $end = now()->startOfDay();
    $workingDays = [];

    for ($date = $start->copy(); $date <= $end; $date->addDay()) {
        if (!$date->isSunday()) {
            $workingDays[] = $date->format('Y-m-d');
        }
    }

    // Absent days = working days - unique attendance days
    $uniqueAttendanceDates = array_unique($attendanceDates);
    $absentDays = count(array_diff($workingDays, $uniqueAttendanceDates));
    $totalWorkingDays = count($workingDays);

    $attendanceRate = $totalWorkingDays > 0
        ? round(($presentDays / $totalWorkingDays) * 100)
        : 0;

    // Leads & performance
    $totalLeads = $user->leads->count();
    $disbursedLeads = $user->leads->where('status', 'disbursed');
    $performanceRate = $totalLeads > 0
        ? round(($disbursedLeads->count() / $totalLeads) * 100)
        : 0;

    $revenue = $disbursedLeads->sum('lead_amount');

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
        'designation' => $user->designation,
        'department' => $user->department,
        'address' => $user->address,
        'created_at' => $user->created_at,
        'profile_photo' => $user->profile_photo,

        // Metrics
        'leads_completed' => $disbursedLeads->count(),
        'performance_rate' => $performanceRate,
        'revenue_generated' => '₹' . number_format($revenue),

        // Attendance
        'attendance_rate' => $attendanceRate,
        'present_days' => $presentDays,
        'half_days' => $halfDays,
        'absent_days' => $absentDays,
        'total_days' => $totalWorkingDays,

        // Others
        'manager' => $user->teamLead->name ?? null,
        'location' => $user->location ?? 'N/A',
        'join_date' => $user->created_at->format('Y-m-d'),
    ]);
}




}
