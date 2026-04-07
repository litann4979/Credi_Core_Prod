<?php

namespace App\Http\Controllers\OpearationController;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperationsReportController extends Controller
{
    public function indexReports(Request $request)
    {
        $today = Carbon::today();

        // ✅ Attendance - All employees' attendance for today
        $attendances = Attendance::whereDate('check_in', $today)->get();

        // ✅ Stats for dashboard cards (across all data)
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

        // ✅ Lead Filtering Logic (no team lead restriction)
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

        $leads = $query->with('employee')->latest()->paginate(10)->withQueryString();


       $tasks = Task::with(['notifications' => function ($query) {
    $query->select('id', 'user_id', 'task_id');
}])
->whereNotNull('operations_id')
->get();


        // ✅ All employees data with performance & attendance rate
        $employees = User::where('designation', 'employee')
            ->whereNull('deleted_at')
            ->with(['attendances', 'leads'])
            ->get()
            ->map(function ($employee) {
                $groupedByDate = $employee->attendances->groupBy('date');
                $presentDays = $groupedByDate->filter(function ($records) {
                    $hasCheckIn = $records->firstWhere('check_in', '!=', null);
                    $hasCheckOut = $records->firstWhere('check_out', '!=', null);
                    return $hasCheckIn && $hasCheckOut;
                })->count();

                $joinDate = Carbon::parse($employee->created_at)->startOfDay();
                $today = now()->startOfDay();
                $totalWorkingDays = 0;

                for ($date = $joinDate->copy(); $date <= $today; $date->addDay()) {
                    if (!$date->isSunday()) {
                        $totalWorkingDays++;
                    }
                }

                $employee->attendance_rate = $totalWorkingDays > 0
                    ? round(($presentDays / $totalWorkingDays) * 100)
                    : 0;

                $totalLeads = $employee->leads->count();
                $disbursedLeads = $employee->leads->where('status', 'disbursed')->count();
                $employee->performance_rate = $totalLeads > 0
                    ? round(($disbursedLeads / $totalLeads) * 100)
                    : 0;

                return $employee;
            });

        return view('Opearation.reports.index', compact('stats', 'attendances', 'leads', 'tasks', 'employees'));
    }

    public function export(Request $request, $type)
    {
        // Reuse the existing export logic from TeamLeadReportController
        $query = Lead::with('teamLead')->whereNull('deleted_at');

        if (in_array($type, ['7', '15', '30'])) {
            $fromDate = Carbon::now()->subDays((int)$type)->startOfDay();
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
}
