<?php

namespace App\Http\Controllers\AdminController;

use App\Events\LiveDashboardUpdated;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\Task;
use App\Models\User;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\FormatHelper;
use Dompdf\Image\Cache;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $tableLeadType = $request->input('table_lead_type', '');
        $tableTeamLeadId = $request->input('table_team_lead_id', '');
        $tableFromDate = $request->input('table_from_date');
        $tableToDate = $request->input('table_to_date');
        $tableMonth = $request->input('table_month', '');


        $pieFromDate = $request->input('pie_from_date');
$pieToDate = $request->input('pie_to_date');
$pieStatus = $request->input('pie_status', '');
        // --------------------------------------------------------
// 1️⃣ GET EMPLOYEES UNDER SELECTED TEAM LEAD
// --------------------------------------------------------
$employeeIds = [];

if ($tableTeamLeadId) {
    $employeeIds = User::where('team_lead_id', $tableTeamLeadId)->pluck('id')->toArray();
}


        // Leads by status (excluding future_lead and creditcard_loan)
        $leadsQuery = Lead::query()->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
            ->whereNotIn('status', ['future_lead'])
            ->whereNotIn('lead_type', ['creditcard_loan']);

        if ($tableLeadType) {
            $leadsQuery->where('lead_type', $tableLeadType);
        }
        if ($tableTeamLeadId) {
            $leadsQuery->whereIn('employee_id', $employeeIds);
        }
        if ($tableMonth) {
            $leadsQuery->where('expected_month', $tableMonth);
        } elseif ($tableFromDate && $tableToDate) {
            // $leadsQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
              $leadsQuery->where(function ($query) use ($tableFromDate, $tableToDate) {
        $query->whereBetween('created_at', [$tableFromDate, $tableToDate])
              ->orWhereBetween('updated_at', [$tableFromDate, $tableToDate]);
    });
        }

        $leadsByStatus = $leadsQuery->groupBy('status')->get()->mapWithKeys(function ($item) {
            return [$item->status => ['count' => $item->count, 'total_valuation' => $item->total_valuation ?? 0]];
        })->toArray();
        $allStatuses = ['personal_lead', 'authorized', 'login', 'approved', 'rejected', 'disbursed'];
        foreach ($allStatuses as $status) {
            if (!isset($leadsByStatus[$status])) {
                $leadsByStatus[$status] = ['count' => 0, 'total_valuation' => 0];
            }
        }
        $totalLeads = array_sum(array_column($leadsByStatus, 'count'));
        $totalValuation = array_sum(array_column($leadsByStatus, 'total_valuation'));

        // Current month leads based on expected_month or custom date range
        $currentMonthQuery = Lead::query()->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
            ->whereNotIn('lead_type', ['creditcard_loan']);

        if ($tableMonth) {
            $currentMonthQuery->where('expected_month', $tableMonth);
        } elseif ($tableFromDate && $tableToDate) {
            // $currentMonthQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
             $currentMonthQuery->where(function ($query) use ($tableFromDate, $tableToDate) {
        $query->whereBetween('created_at', [$tableFromDate, $tableToDate])
              ->orWhereBetween('updated_at', [$tableFromDate, $tableToDate]);
    });
        } else {
            $currentMonth = Carbon::now()->format('F'); // Returns "July"
            $currentMonthQuery->where('expected_month', $currentMonth); // Default to July if no custom dates or month
        }

        if ($tableLeadType) {
            $currentMonthQuery->where('lead_type', $tableLeadType);
        }
        if ($tableTeamLeadId) {
             $currentMonthQuery->whereIn('employee_id', $employeeIds);
        }

        $currentMonthLeads = $currentMonthQuery->groupBy('status')->get()->mapWithKeys(function ($item) {
            return [$item->status => ['count' => $item->count, 'total_valuation' => $item->total_valuation ?? 0]];
        })->toArray();
        foreach (['login', 'approved', 'rejected', 'disbursed'] as $status) {
            $leadsByStatus[$status] = $currentMonthLeads[$status] ?? ['count' => 0, 'total_valuation' => 0];
        }

        // Future leads query
        $futureLeadsQuery = Lead::where('status', 'future_lead');

        if ($tableLeadType) {
            $futureLeadsQuery->where('lead_type', $tableLeadType);
        } elseif ($tableFromDate && $tableToDate) {
            $futureLeadsQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
        } else {
            $futureLeadsQuery->whereNot('lead_type','creditcard_loan'); // Default to July if no custom dates or month
        }
        if ($tableTeamLeadId) {
            $futureLeadsQuery->whereIn('employee_id', $employeeIds);
        }

        $futureLeadCount = $futureLeadsQuery->count();
        $leadsByStatus['future_lead'] = ['count' => $futureLeadCount, 'total_valuation' => 0];
// Today's leads (created or status changed today, excluding creditcard_loan)
    $createdTodayLeadIds = Lead::whereDate('created_at', Carbon::today())
        ->pluck('id')
        ->toArray();

    $statusChangedLeadIds = LeadHistory::where('action', 'status_changed')
        ->whereDate('created_at', Carbon::today())
        ->pluck('lead_id')
        ->toArray();

    $allRelevantLeadIds = array_unique(array_merge($createdTodayLeadIds, $statusChangedLeadIds));

    $todays_lead_type = $request->input('todays_lead_type', ''); // Get the lead_type filter value
    $todaysLeadsByStatusQuery = Lead::whereIn('id', $allRelevantLeadIds)
        ->whereNotIn('lead_type', ['creditcard_loan'])
        ->when($todays_lead_type, fn($q) => $q->where('lead_type', $todays_lead_type))
        ->when($tableTeamLeadId, fn($q) => $q->where('team_lead_id', $tableTeamLeadId))
        ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
        ->groupBy('status');

    $todaysLeadsByStatus = $todaysLeadsByStatusQuery
        ->get()
        ->mapWithKeys(function ($item) {
            $colorClass = match($item->status) {
                'personal_lead' => 'bg-blue-100 text-blue-800',
                'authorized' => 'bg-emerald-100 text-emerald-800',
                'login' => 'bg-amber-100 text-amber-800',
                'approved' => 'bg-violet-100 text-violet-800',
                'rejected' => 'bg-red-100 text-red-800',
                'disbursed' => 'bg-cyan-100 text-cyan-800',
                'future_lead' => 'bg-lime-100 text-lime-800',
                default => 'bg-gray-100 text-gray-800',
            };
            return [$item->status => [
                'count' => $item->count,
                'total_valuation' => $item->total_valuation ?? 0,
                'colorClass' => $colorClass
            ]];
        })->toArray();

    $allStatuses = ['personal_lead', 'authorized', 'login', 'approved', 'rejected', 'disbursed', 'future_lead'];
    foreach ($allStatuses as $status) {
        if (!isset($todaysLeadsByStatus[$status])) {
            $todaysLeadsByStatus[$status] = [
                'count' => 0,
                'total_valuation' => 0,
                'colorClass' => match($status) {
                    'personal_lead' => 'bg-blue-100 text-blue-800',
                    'authorized' => 'bg-emerald-100 text-emerald-800',
                    'login' => 'bg-amber-100 text-amber-800',
                    'approved' => 'bg-violet-100 text-violet-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'disbursed' => 'bg-cyan-100 text-cyan-800',
                    'future_lead' => 'bg-lime-100 text-lime-800',
                    default => 'bg-gray-100 text-gray-800',
                }
            ];
        }
    }


// Credit card leads
$creditCardFromDate = $request->input('credit_card_from_date');
$creditCardToDate = $request->input('credit_card_to_date');
$creditCardMonth = $request->input('credit_card_month', Carbon::now()->format('F')); // Default to current month
 $creditCardStatus = $request->input('credit_card_status', ''); // New status filter


$creditCardLeadsQuery = Lead::query()
    ->select('id', 'name','phone', 'email', 'dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id')
    ->where('lead_type', 'creditcard_loan');

// ✅ Apply Status filter if selected
if ($creditCardStatus) {
    $creditCardLeadsQuery->where('status', $creditCardStatus);
}

// ✅ Apply date filters
if ($creditCardFromDate && $creditCardToDate) {
    $creditCardLeadsQuery->whereBetween('created_at', [$creditCardFromDate, $creditCardToDate]);
}


$creditCardLeads = $creditCardLeadsQuery
    ->orderBy('updated_at', 'desc') // ✅ Latest leads first
    ->get();

        //chart data
        // $chartDateFilter = $request->input('chart_date_filter', '30_days');
        // $chartStartDate = $request->input('chart_start_date');
        // $chartEndDate = $request->input('chart_end_date');
         $operationId = $request->input('operation_id');
         $teamLeadId = $request->input('team_lead_id');
         $employeeId = $request->input('employee_id');

        // $baseLeadsQuery = Lead::query()->with(['employee' => function ($query) {
        //     $query->select('id', 'name', 'team_lead_id', 'created_by');
        // }])->whereNotIn('lead_type', ['creditcard_loan']);

        // if ($chartDateFilter === 'custom' && $chartStartDate && $chartEndDate) {
        //     $baseLeadsQuery->whereBetween('created_at', [$chartStartDate, $chartEndDate]);
        // } elseif ($chartDateFilter) {
        //     $days = match ($chartDateFilter) {
        //         '7_days' => 7,
        //         '15_days' => 15,
        //         '30_days' => 30,
        //         default => 30,
        //     };
        //     $baseLeadsQuery->where('created_at', '>=', Carbon::now()->subDays($days));
        // }

        // if ($operationId) {
        //     $baseLeadsQuery->whereHas('employee', function ($query) use ($operationId) {
        //         $query->whereIn('team_lead_id', User::where('designation', 'team_lead')
        //             ->where('created_by', $operationId)
        //             ->pluck('id'));
        //     });
        // }

        // if ($teamLeadId) {
        //     $baseLeadsQuery->whereHas('employee', function ($query) use ($teamLeadId) {
        //         $query->where('team_lead_id', $teamLeadId);
        //     });
        // }

        // if ($employeeId) {
        //     $baseLeadsQuery->where('employee_id', $employeeId);
        // }

        // $leadsPerEmployee = (clone $baseLeadsQuery)
        //     ->select('employee_id', DB::raw('COUNT(*) as lead_count'))
        //     ->groupBy('employee_id')
        //     ->get()
        //     ->map(function ($item) {
        //         return ['name' => $item->employee->name ?? 'Unknown', 'lead_count' => $item->lead_count];
        //     });

        // $leadStatusDistribution = (clone $baseLeadsQuery)
        //     ->select('status', DB::raw('COUNT(*) as count'))
        //     ->groupBy('status')
        //     ->get()
        //     ->mapWithKeys(function ($item) {
        //         return [$item->status => $item->count];
        //     })->toArray();




        $operations = User::where('designation', 'operations')->select('id', 'name')->get();
        $teamLeads = User::where('designation', 'team_lead')
            ->when($operationId, function ($query) use ($operationId) {
                return $query->where('created_by', $operationId);
            })
            ->select('id', 'name')
            ->get();
        $employees = User::where('designation', 'employee')
            ->when($teamLeadId, function ($query) use ($teamLeadId) {
                return $query->where('team_lead_id', $teamLeadId);
            })
            ->select('id', 'name')
            ->get();

        $search = $request->input('search', '');
        $status = $request->input('status', '');
        $state = $request->input('state', '');
        $district = $request->input('district', '');
        $city = $request->input('city', '');
        $leadType = $request->input('lead_type', '');
        $leadOperationId = $request->input('lead_operation_id', '');
        $leadTeamLeadId = $request->input('lead_team_lead_id', '');
        $leadEmployeeId = $request->input('lead_employee_id', '');
        $minAmount = $request->input('min_amount', '');
        $maxAmount = $request->input('max_amount', '');
        $leadDateFilter = $request->input('lead_date_filter', '');
        $leadStartDate = $request->input('lead_start_date', '');
        $leadEndDate = $request->input('lead_end_date', '');

        $leadsQuery = Lead::query()
            ->select('id', 'name', 'email', 'dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id')
            ->with(['employee' => function ($query) {
                $query->select('id', 'name');
            }])
            ->whereNotIn('lead_type', ['creditcard_loan']);

        if ($search) {
            $leadsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $leadsQuery->where('status', $status);
        }

        if ($state) {
            $leadsQuery->where('state', $state);
        }

        if ($district) {
            $leadsQuery->where('district', $district);
        }

        if ($city) {
            $leadsQuery->where('city', $city);
        }

        if ($leadType) {
            $leadsQuery->where('lead_type', $leadType);
        }

        if ($leadOperationId) {
            $leadsQuery->whereHas('employee', function ($query) use ($leadOperationId) {
                $query->whereIn('team_lead_id', User::where('designation', 'team_lead')
                    ->where('created_by', $leadOperationId)
                    ->pluck('id'));
            });
        }

        if ($leadTeamLeadId) {
            $leadsQuery->whereHas('employee', function ($query) use ($leadTeamLeadId) {
                $query->where('team_lead_id', $leadTeamLeadId);
            });
        }

        if ($leadEmployeeId) {
            $leadsQuery->where('employee_id', $leadEmployeeId);
        }

        if ($minAmount) {
            $leadsQuery->where('lead_amount', '>=', $minAmount);
        }

        if ($maxAmount) {
            $leadsQuery->where('lead_amount', '<=', $maxAmount);
        }

        if ($leadDateFilter === 'custom' && $leadStartDate && $leadEndDate) {
            $leadsQuery->whereBetween('created_at', [$leadStartDate, $leadEndDate]);
        } elseif ($leadDateFilter) {
            $days = match ($leadDateFilter) {
                '7_days' => 7,
                '15_days' => 15,
                '30_days' => 30,
                '60_days' => 60,
                default => null,
            };
            if ($days) {
                $leadsQuery->where('created_at', '>=', Carbon::now()->subDays($days));
            }
        }

        $leads = $leadsQuery->get();

        $statuses = Lead::select('status')->distinct()->pluck('status')->toArray();
        $states = State::where('status', 'Active')->select('state_id', 'state_title')->get();
        $districts = District::where('district_status', 'Active')
            ->when($state, function ($query) use ($state) {
                return $query->whereIn('state_id', State::where('state_title', $state)
                    ->where('status', 'Active')
                    ->pluck('state_id'));
            })
            ->select('districtid', 'district_title')
            ->get();
        $cities = City::where('status', 'Active')
            ->when($district, function ($query) use ($district) {
                return $query->whereIn('districtid', District::where('district_title', $district)
                    ->where('district_status', 'Active')
                    ->pluck('districtid'));
            })
            ->select('id', 'name')
            ->get();
        $leadTypes = Lead::select('lead_type')->distinct()->whereNotIn('lead_type', ['creditcard_loan'])->pluck('lead_type')->filter()->toArray();

        $taskDateFilter = $request->input('task_date_filter', '30_days');
        $taskStartDate = $request->input('task_start_date');
        $taskEndDate = $request->input('task_end_date');
        $taskTargetType = $request->input('task_target_type', '');
        $taskOperationId = $request->input('task_operation_id', '');
        $taskTeamLeadId = $request->input('task_team_lead_id', '');
        $taskEmployeeId = $request->input('task_employee_id', '');

        $tasksQuery = Task::query()
            ->where('admin_id', auth()->id())
            ->with(['teamLead:id,name', 'assignedUsers:id,name'])
            ->select('id', 'title', 'target_type', 'priority', 'status', 'assigned_date', 'due_date', 'progress', 'team_lead_id');

        if ($taskDateFilter === 'custom' && $taskStartDate && $taskEndDate) {
            $tasksQuery->whereBetween('assigned_date', [$taskStartDate, $taskEndDate]);
        } elseif ($taskDateFilter) {
            $days = match ($taskDateFilter) {
                '7_days' => 7,
                '15_days' => 15,
                '30_days' => 30,
                '60_days' => 60,
                default => 30,
            };
            $tasksQuery->where('assigned_date', '>=', Carbon::now()->subDays($days));
        }

        if ($taskTargetType) {
            $tasksQuery->where('target_type', $taskTargetType);
        }

        if ($taskOperationId) {
            $tasksQuery->where('operations_id', $taskOperationId);
        }

        if ($taskTeamLeadId) {
            $tasksQuery->where('team_lead_id', $teamLeadId);
        }

        if ($taskEmployeeId) {
            $tasksQuery->whereHas('assignedUsers', function ($query) use ($taskEmployeeId) {
                $query->where('users.id', $taskEmployeeId);
            });
        }

        $tasks = $tasksQuery->get();

        $taskTargetTypes = ['all', 'individual', 'individual_teamlead', 'individual_operation'];

        $attendanceDate = $request->input('attendance_date', Carbon::today()->toDateString());
        $attendanceRoleFilter = $request->input('attendance_role_filter', 'all');

        $attendanceQuery = Attendance::query()
            ->with(['employee:id,name,designation', 'teamLead:id,name,designation', 'operation:id,name,designation'])
            ->whereDate('date', $attendanceDate);

        if ($attendanceRoleFilter === 'team_lead') {
            $attendanceQuery->whereHas('teamLead', function ($query) {
                $query->where('designation', 'team_lead');
            });
        } elseif ($attendanceRoleFilter === 'employee') {
            $attendanceQuery->whereHas('employee', function ($query) {
                $query->where('designation', 'employee');
            });
        } elseif ($attendanceRoleFilter === 'operation') {
            $attendanceQuery->whereHas('operation', function ($query) {
                $query->where('designation', 'operations');
            });
        }

        $attendances = $attendanceQuery->get();

        $allUsers = User::select('id', 'name', 'designation')->get();
        $teamLeads = $allUsers->where('designation', 'team_lead');
        $employees = $allUsers->where('designation', 'employee');
        $operations = $allUsers->where('designation', 'operations');



// Query for pie chart data
$pieChartQuery = Lead::query()
    ->select(
        'employee_id',
        DB::raw('COUNT(*) as leads_count'),
        DB::raw('SUM(lead_amount) as total_amount')
    )
    ->with(['employee:id,name'])
    ->whereNotIn('lead_type', ['creditcard_loan']); // Exclude unwanted loan type


// Apply date filter (created OR updated)
if ($pieFromDate && $pieToDate) {
    $pieChartQuery->where(function($query) use ($pieFromDate, $pieToDate) {
        $query->whereBetween('created_at', [$pieFromDate, $pieToDate])
              ->orWhereBetween('updated_at', [$pieFromDate, $pieToDate]);
    });
}


// Apply status filter if selected
if ($pieStatus) {
    $pieChartQuery->where('status', $pieStatus);
}


// Group and sort after filters
$pieChartQuery->groupBy('employee_id')
              ->orderByDesc('total_amount');


// Execute query
$pieChartResults = $pieChartQuery->get();


// Prepare response for chart
$pieChartData = [
    'employees'     => $pieChartResults->pluck('employee.name')->toArray(),
    'leadsCount'    => $pieChartResults->pluck('leads_count')->toArray(),
    'totalAmounts'  => $pieChartResults->pluck('total_amount')->toArray()
];


        return view('admin.dashboard', compact(
            'totalLeads', 'totalValuation',
            'leadsByStatus','todaysLeadsByStatus','operations', 'teamLeads', 'employees',
            'tableFromDate', 'tableToDate', 'tableLeadType', 'tableTeamLeadId', 'tableMonth',
           'creditCardLeads', 'creditCardFromDate', 'creditCardToDate', 'creditCardMonth',
             'todays_lead_type',
            'operationId', 'teamLeadId', 'employeeId',
            'leads', 'statuses', 'states', 'districts', 'cities', 'leadTypes',
            'search', 'status', 'state', 'district', 'city', 'leadType',
            'leadOperationId', 'leadTeamLeadId', 'leadEmployeeId', 'minAmount', 'maxAmount',
            'leadDateFilter', 'leadStartDate', 'leadEndDate',
            'tasks', 'taskDateFilter', 'taskStartDate', 'taskEndDate',
            'taskTargetType', 'taskOperationId', 'taskTeamLeadId', 'taskEmployeeId', 'taskTargetTypes',
            'attendances', 'attendanceDate', 'attendanceRoleFilter', 'allUsers', 'teamLeads', 'employees', 'operations',
             'pieChartData', 'pieFromDate', 'pieToDate', 'pieStatus','creditCardStatus',

        ));
    }


    public function show(Request $request)
    {
        // Optionally pass leadId to the view
        $leadId = $request->query('leadId');
        return view('admin.creditcardlead-details', compact('leadId'));
    }

   public function leadsDetails(Request $request)
{
    $status = $request->input('status');
    $leadType = $request->input('lead_type', '');
    $teamLeadId = $request->input('team_lead_id', '');
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $month = $request->input('month', '');
    $executiveFilter = $request->input('executive');

    // New filter parameters
    $nameFilter = $request->input('name');
    $phoneFilter = $request->input('phone');
    $loanAccountFilter = $request->input('loan_account');
    $companyFilter = $request->input('company');
    $loanAmountFilter = $request->input('loan_amount');
    $statusFilter = $request->input('status_filter');
    $leadTypeFilter = $request->input('lead_type_filter');
    $bankFilter = $request->input('bank');

       // ------------------------------------------------------------
    // 1️⃣ FETCH EMPLOYEES UNDER SELECTED TEAM LEAD
    // ------------------------------------------------------------
    $employeeIds = [];

    if ($teamLeadId) {
        $employeeIds = User::where('team_lead_id', $teamLeadId)->pluck('id')->toArray();
    }

    $leadsQuery = Lead::query()
        ->select('id', 'name', 'email', 'phone','loan_account_number','dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id', 'updated_at')
        ->with(['employee' => function ($query) {
            $query->select('id', 'name');
        }])
        ->orderBy('updated_at', 'desc');

    // Add executive filter condition - now filtering by employee_id
    if ($executiveFilter) {
        $leadsQuery->where('employee_id', $executiveFilter);
    }

    if ($status !== 'total') {
        $leadsQuery->where('status', $status);
    } else {
        $leadsQuery->whereNotIn('status', ['future_lead']);
    }

    if ($leadType) {
        $leadsQuery->where('lead_type', $leadType);
    }

    if ($teamLeadId) {
        $leadsQuery->whereIn('employee_id', $employeeIds);
    }

    // Apply month filter only for non-future_lead statuses
    if ($month && $status !== 'future_lead') {
        $leadsQuery->where('expected_month', $month);
    } elseif ($fromDate && $toDate) {
        // $leadsQuery->whereBetween('created_at', [$fromDate, $toDate]);
          $leadsQuery->where(function ($query) use ($fromDate, $toDate) {
        $query->whereBetween('created_at', [$fromDate, $toDate])
              ->orWhereBetween('updated_at', [$fromDate, $toDate]);
    });
    } else {
        $statusesWithCurrentMonthFilter = ['login', 'approved', 'rejected', 'disbursed'];
        if (in_array($status, $statusesWithCurrentMonthFilter)) {
            $currentMonth = Carbon::now()->format('F'); // Returns "August"
            $leadsQuery->where('expected_month', $currentMonth);
        }
    }

    if ($status !== 'future_lead') {
        $leadsQuery->whereNotIn('lead_type', ['creditcard_loan']);
    }

    // Apply the new filters
    if ($nameFilter) {
        $leadsQuery->where('name', 'like', '%' . $nameFilter . '%');
    }

    if ($phoneFilter) {
        $leadsQuery->where('phone', 'like', '%' . $phoneFilter . '%');
    }

    if ($loanAccountFilter) {
        $leadsQuery->where('loan_account_number', 'like', '%' . $loanAccountFilter . '%');
    }

    if ($companyFilter) {
        $leadsQuery->where('company_name', 'like', '%' . $companyFilter . '%');
    }

  // Apply loan amount filter (you might want to implement range filtering)
    if ($loanAmountFilter) {
    if ($loanAmountFilter === '1-1000') {
        $leadsQuery->whereBetween('leads.lead_amount', [1, 1000]);
    } elseif ($loanAmountFilter === '1000-10000') {
        $leadsQuery->whereBetween('leads.lead_amount', [1000, 10000]);
    }elseif ($loanAmountFilter === '10000-100000') {
        $leadsQuery->whereBetween('leads.lead_amount', [10000, 100000]);
    } elseif ($loanAmountFilter === '100000-1000000') {
        $leadsQuery->whereBetween('leads.lead_amount', [100000, 1000000]);
    } elseif ($loanAmountFilter === '1000000+') {
        $leadsQuery->where('leads.lead_amount', '>', 1000000);
    }
    }

    if ($statusFilter) {
        $leadsQuery->where('status', $statusFilter);
    }

    if ($leadTypeFilter) {
        $leadsQuery->where('lead_type', $leadTypeFilter);
    }

    if ($bankFilter) {
        $leadsQuery->where('bank_name', $bankFilter);
    }

    $leads = $leadsQuery->get()->map(function ($lead) {
        $statusColorClass = match($lead->status) {
            'personal_lead' => 'bg-blue-100 text-blue-800',
            'authorized' => 'bg-emerald-100 text-emerald-800',
            'login' => 'bg-amber-100 text-amber-800',
            'approved' => 'bg-violet-100 text-violet-800',
            'rejected' => 'bg-red-100 text-red-800',
            'disbursed' => 'bg-cyan-100 text-cyan-800',
            'future_lead' => 'bg-lime-100 text-lime-800',
            default => 'bg-gray-100 text-gray-800',
        };

        return [
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email ?? 'N/A',
            'phone' => $lead->phone ?? 'N/A',
            'loan_account_number'=> $lead->loan_account_number ?? 'N/A',
            'dob' => $lead->dob?$lead->dob->format('Y-m-d') : null,
            'city' => $lead->city ?? 'N/A',
            'district' => $lead->district ?? 'N/A',
            'state' => $lead->state ?? 'N/A',
            'company_name' => $lead->company_name ?? 'N/A',
            'lead_amount' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0),
            'salary' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->salary ?? 0),
            'status' => ucfirst(str_replace('_', ' ', $lead->status)),
            'status_color' => $statusColorClass,
            'lead_type' => $this->getShortLeadType($lead->lead_type ?? 'N/A'),
            'employee_name' => $lead->employee->name ?? 'N/A',
            'turnover_amount' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->turnover_amount ?? 0),
            'bank_name' => $lead->bank_name ?? 'N/A',
            'updated_at' => $lead->updated_at ? $lead->updated_at->format('Y-m-d H:i') : 'N/A',

        ];
    })->toArray();

    // Get executives for dropdown - only users with employee role
    $executives = User::select('id', 'name')
        ->where('designation', 'employee') // or whatever condition identifies executives
        ->whereHas('leads')
        ->orderBy('name')
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name
            ];
        })->toArray();

    // Get team leads for dropdown
    $teamLeads = User::select('id', 'name')
        ->where('designation', 'team_lead')
        ->orderBy('name')
        ->get();

    // Get unique values for filter dropdowns
    $names = Lead::distinct()->whereNotNull('name')->orderBy('name')->pluck('name')->toArray();
    $phones = Lead::distinct()->whereNotNull('phone')->orderBy('phone')->pluck('phone')->toArray();
    $loanAccounts = Lead::distinct()->whereNotNull('loan_account_number')->orderBy('loan_account_number')->pluck('loan_account_number')->toArray();
    $companies = Lead::distinct()->whereNotNull('company_name')->orderBy('company_name')->pluck('company_name')->toArray();
    $loanAmounts = Lead::distinct()->whereNotNull('lead_amount')->orderBy('lead_amount')->pluck('lead_amount')->toArray();
    $statuses = Lead::distinct()->whereNotNull('status')->orderBy('status')->pluck('status')->toArray();
    $leadTypes = Lead::distinct()->whereNotNull('lead_type')->orderBy('lead_type')->pluck('lead_type')->toArray();
    $banks = Lead::distinct()->whereNotNull('bank_name')->orderBy('bank_name')->pluck('bank_name')->toArray();

    $banksName = \App\Models\Bank::where('is_active', true)
                ->pluck('bank_name')
                ->toArray();

   $formattedLeadTypes = array_map(function($type) {
    return [
        'value' => $type,
        'display' => $this->getShortLeadType($type)
    ];
}, array_filter($leadTypes, function($type) {
    return $type !== 'creditcard_loan'; // exclude this one
}));

    return view('admin.leads-details', compact(
        'banksName',
        'leads',
        'status',
        'leadType',
        'teamLeadId',
        'teamLeads',
        'fromDate',
        'toDate',
        'month',
        'executives',
        'executiveFilter',
        'names',
        'phones',
        'loanAccounts',
        'companies',
        'loanAmounts',
        'statuses',
        'leadTypes',
        'banks',
        'nameFilter',
        'phoneFilter',
        'loanAccountFilter',
        'companyFilter',
        'loanAmountFilter',
        'statusFilter',
        'leadTypeFilter',
        'bankFilter',
        'formattedLeadTypes',
    ));
}


private function getShortLeadType($leadType)
{
    $mapping = [
        'personal_loan' => 'PL',
        'business_loan' => 'BL',
        'home_loan' => 'HL',
        'loan_against_property' => 'LAP',
        'creditcard_loan' => 'CCL',
        // Add more mappings as needed
    ];

    return $mapping[$leadType] ?? $leadType;
}

 public function todayLeads(Request $request)
{
    $status = $request->input('status');
    $leadType = $request->input('lead_type', ''); // Retrieve lead_type from request
    $executiveFilter = $request->input('executive');


     // New filter parameters
    $nameFilter = $request->input('name');
    $phoneFilter = $request->input('phone');
    $loanAccountFilter = $request->input('loan_account');
    $companyFilter = $request->input('company');
    $loanAmountFilter = $request->input('loan_amount');
    $statusFilter = $request->input('status_filter');
    $leadTypeFilter = $request->input('lead_type_filter');
    $bankFilter = $request->input('bank');


    $createdTodayLeadIds = Lead::whereDate('created_at', Carbon::today())
        ->pluck('id')
        ->toArray();

    $statusChangedLeadIds = LeadHistory::where('action', 'status_changed')
        ->whereDate('created_at', Carbon::today())
        ->pluck('lead_id')
        ->toArray();

    $allRelevantLeadIds = array_unique(array_merge($createdTodayLeadIds, $statusChangedLeadIds));

    $leadsQuery = Lead::whereIn('id', $allRelevantLeadIds)
        ->whereNotIn('lead_type', ['creditcard_loan']);


    if ($executiveFilter) {
        $leadsQuery->where('employee_id', $executiveFilter);
    }

    if ($status) {
        $leadsQuery->where('status', $status);
    }

    if ($leadType) {
        $leadsQuery->where('lead_type', $leadType);
    }

    // Apply the new filters
    if ($nameFilter) {
        $leadsQuery->where('name', 'like', '%' . $nameFilter . '%');
    }

    if ($phoneFilter) {
        $leadsQuery->where('phone', 'like', '%' . $phoneFilter . '%');
    }

    if ($loanAccountFilter) {
        $leadsQuery->where('loan_account_number', 'like', '%' . $loanAccountFilter . '%');
    }

    if ($companyFilter) {
        $leadsQuery->where('company_name', 'like', '%' . $companyFilter . '%');
    }

    // Apply loan amount filter (you might want to implement range filtering)
    if ($loanAmountFilter) {
    if ($loanAmountFilter === '1-1000') {
        $leadsQuery->whereBetween('leads.lead_amount', [1, 1000]);
    } elseif ($loanAmountFilter === '1000-10000') {
        $leadsQuery->whereBetween('leads.lead_amount', [1000, 10000]);
    }elseif ($loanAmountFilter === '10000-100000') {
        $leadsQuery->whereBetween('leads.lead_amount', [10000, 100000]);
    } elseif ($loanAmountFilter === '100000-1000000') {
        $leadsQuery->whereBetween('leads.lead_amount', [100000, 1000000]);
    } elseif ($loanAmountFilter === '1000000+') {
        $leadsQuery->where('leads.lead_amount', '>', 1000000);
    }
    }

    if ($statusFilter) {
        $leadsQuery->where('status', $statusFilter);
    }

    if ($leadTypeFilter) {
        $leadsQuery->where('lead_type', $leadTypeFilter);
    }

    if ($bankFilter) {
        $leadsQuery->where('bank_name', 'like', '%' . $bankFilter . '%');
    }

    $leadsQuery->orderBy('updated_at', 'desc');
    $leads = $leadsQuery->select('id', 'name', 'email', 'phone', 'loan_account_number', 'dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id','updated_at')
        ->with(['employee' => function ($query) {
            $query->select('id', 'name');
        }])
        ->get()
        ->map(function ($lead) {
            $statusColorClass = match($lead->status) {
                'personal_lead' => 'bg-blue-100 text-blue-800',
                'authorized' => 'bg-cyan-100 text-cyan-800',
                'login' => 'bg-amber-100 text-amber-800',
                'approved' => 'bg-violet-100 text-violet-800',
                'rejected' => 'bg-red-100 text-red-800',
                'disbursed' => 'bg-emerald-100 text-emerald-800', // Fixed typo: 'emerland' to 'emerald'
                'future_lead' => 'bg-lime-100 text-lime-800',
                default => 'bg-gray-100 text-gray-800',
            };

            return [
                'id' => $lead->id,
                'name' => $lead->name,
                'phone' => $lead->phone ?? 'N/A',
                'loan_account_number' => $lead->loan_account_number ?? 'N/A',
                'email' => $lead->email ?? 'N/A',
                'dob' => $lead->dob ? $lead->dob->format('Y-m-d') : 'N/A',
                'city' => $lead->city ?? 'N/A',
                'district' => $lead->district ?? 'N/A',
                'state' => $lead->state ?? 'N/A',
                'company_name' => $lead->company_name ?? 'N/A',
                'lead_amount' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0),
                'salary' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->salary ?? 0),
                'status' => ucfirst(str_replace('_', ' ', $lead->status)),
                'status_color' => $statusColorClass,
                'lead_type' => $this->getShortLeadType($lead->lead_type ?? 'N/A'),
                'employee_name' => $lead->employee->name ?? 'N/A',
                'turnover_amount' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->turnover_amount ?? 0),
                'bank_name' => $lead->bank_name ?? 'N/A',
                'updated_at' => $lead->updated_at ? $lead->updated_at->format('Y-m-d') : 'N/A',
            ];
        })->toArray();

        // Get executives for dropdown - only users with employee role who have leads
    $executives = User::select('id', 'name')
        ->where('designation', 'employee')
        ->whereHas('leads')
        ->orderBy('name')
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name
            ];
        })->toArray();

        // Get unique values for filter dropdowns
    $names = Lead::distinct()->whereNotNull('name')->orderBy('name')->pluck('name')->toArray();
    $phones = Lead::distinct()->whereNotNull('phone')->orderBy('phone')->pluck('phone')->toArray();
    $loanAccounts = Lead::distinct()->whereNotNull('loan_account_number')->orderBy('loan_account_number')->pluck('loan_account_number')->toArray();
    $companies = Lead::distinct()->whereNotNull('company_name')->orderBy('company_name')->pluck('company_name')->toArray();
    $loanAmounts = Lead::distinct()->whereNotNull('lead_amount')->orderBy('lead_amount')->pluck('lead_amount')->toArray();
    $statuses = Lead::distinct()->whereNotNull('status')->orderBy('status')->pluck('status')->toArray();
    $leadTypes = Lead::distinct()->whereNotNull('lead_type')->orderBy('lead_type')->pluck('lead_type')->toArray();
    $banks = Lead::distinct()->whereNotNull('bank_name')->orderBy('bank_name')->pluck('bank_name')->toArray();

   $formattedLeadTypes = array_map(function($type) {
    return [
        'value' => $type,
        'display' => $this->getShortLeadType($type)
    ];
}, array_filter($leadTypes, function($type) {
    return $type !== 'creditcard_loan'; // exclude this one
}));
    return view('admin.today-leads', compact('leads', 'status', 'leadType', 'executives', 'executiveFilter','names','phones','loanAccounts','companies','loanAmounts','statuses','leadTypes','banks',
    'formattedLeadTypes','nameFilter',
    'phoneFilter',
    'loanAccountFilter',
    'companyFilter',
    'loanAmountFilter',
    'statusFilter',
    'leadTypeFilter',
    'bankFilter'));
}

//     public function getLeadsByStatus(Request $request)
// {
//     $status = $request->input('status');
//     $leadType = $request->input('lead_type', '');
//     $fromDate = $request->input('from_date');
//     $toDate = $request->input('to_date');

//     $leadsQuery = Lead::query()
//         ->select('id', 'name', 'email', 'dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id')
//         ->with(['employee' => function ($query) {
//             $query->select('id', 'name');
//         }]);

//     if ($status !== 'total') {
//         $leadsQuery->where('status', $status);
//     } else {
//         $leadsQuery->whereNotIn('status', ['future_lead']);
//     }

//     if ($leadType) {
//         $leadsQuery->where('lead_type', $leadType);
//     }

//     if ($fromDate && $toDate) {
//         $leadsQuery->whereBetween('created_at', [$fromDate, $toDate]);
//     } else {
//         // Apply current month filter for future_lead and other statuses when no custom dates are provided
//         $statusesWithCurrentMonthFilter = ['login', 'approved', 'rejected', 'disbursed', 'future_lead'];
//         if (in_array($status, $statusesWithCurrentMonthFilter)) {
//             $leadsQuery->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
//         }
//     }

//     if ($status !== 'future_lead') {
//         $leadsQuery->whereNotIn('lead_type', ['creditcard_loan']);
//     }

//     $leads = $leadsQuery->get()->map(function ($lead) {
//         $statusColorClass = match($lead->status) {
//             'personal_lead' => 'bg-blue-100 text-blue-800',
//             'authorized' => 'bg-emerald-100 text-emerald-800',
//             'login' => 'bg-amber-100 text-amber-800',
//             'approved' => 'bg-violet-100 text-violet-800',
//             'rejected' => 'bg-red-100 text-red-800',
//             'disbursed' => 'bg-cyan-100 text-cyan-800',
//             'future_lead' => 'bg-lime-100 text-lime-800',
//             default => 'bg-gray-100 text-gray-800',
//         };

//         return [
//             'id' => $lead->id,
//             'name' => $lead->name,
//             'email' => $lead->email ?? 'N/A',
//             'dob' => $lead->dob ? $lead->dob->format('Y-m-d') : 'N/A',
//             'city' => $lead->city ?? 'N/A',
//             'district' => $lead->district ?? 'N/A',
//             'state' => $lead->state ?? 'N/A',
//             'company_name' => $lead->company_name ?? 'N/A',
//             'lead_amount' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0),
//             'salary' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->salary ?? 0),
//             'status' => ucfirst(str_replace('_', ' ', $lead->status)),
//             'status_color' => $statusColorClass,
//             'lead_type' => $lead->lead_type ?? 'N/A',
//             'turnover_amount' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->turnover_amount ?? 0),
//             'bank_name' => $lead->bank_name ?? 'N/A',
//         ];
//     })->toArray();

//     return response()->json(['leads' => $leads]);
// }


    public function filterLeads(Request $request)
    {
        // Validate request
        $request->validate([
            'table_from_date' => 'nullable|date',
            'table_to_date' => 'nullable|date|after_or_equal:table_from_date',
            'table_lead_type' => 'nullable|string',
        ]);

        $tableLeadType = $request->input('table_lead_type', '');
        $tableFromDate = $request->input('table_from_date');
        $tableToDate = $request->input('table_to_date');

        // Leads by status
        $leadsQuery = Lead::query()->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
            ->whereNotIn('status', ['future_lead'])
            ->whereNotIn('lead_type', ['creditcard_loan']);

        if ($tableLeadType) {
            $leadsQuery->where('lead_type', $tableLeadType);
        }
        if ($tableFromDate && $tableToDate) {
            $leadsQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
        }

        $leadsByStatus = $leadsQuery->groupBy('status')->get()->mapWithKeys(function ($item) {
            return [$item->status => ['count' => $item->count, 'total_valuation' => $item->total_valuation ?? 0]];
        })->toArray();

        // Ensure all statuses are present
        $allStatuses = ['personal_lead', 'authorized', 'login', 'approved', 'rejected', 'disbursed'];
        foreach ($allStatuses as $status) {
            if (!isset($leadsByStatus[$status])) {
                $leadsByStatus[$status] = ['count' => 0, 'total_valuation' => 0];
            }
        }

        // Current month leads for login, approved, rejected, disbursed
        $currentMonthQuery = Lead::query()->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
            ->whereNotIn('lead_type', ['creditcard_loan'])
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);

        if ($tableLeadType) {
            $currentMonthQuery->where('lead_type', $tableLeadType);
        }
        if ($tableFromDate && $tableToDate) {
            $currentMonthQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
        }

        $currentMonthLeads = $currentMonthQuery->groupBy('status')->get()->mapWithKeys(function ($item) {
            return [$item->status => ['count' => $item->count, 'total_valuation' => $item->total_valuation ?? 0]];
        })->toArray();

        // Override counts for login, approved, rejected, disbursed
        foreach (['login', 'approved', 'rejected', 'disbursed'] as $status) {
            $leadsByStatus[$status] = $currentMonthLeads[$status] ?? ['count' => 0, 'total_valuation' => 0];
        }

        // Future leads count
        $futureLeadsQuery = Lead::where('status', 'future_lead')
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);

        if ($tableLeadType) {
            $futureLeadsQuery->where('lead_type', $tableLeadType);
        }
        if ($tableFromDate && $tableToDate) {
            $futureLeadsQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
        }

        $leadsByStatus['future_lead'] = ['count' => $futureLeadsQuery->count(), 'total_valuation' => 0];

        // Format response
        $response = [
            'totalLeads' => [
                'count' => array_sum(array_column($leadsByStatus, 'count')),
                'valuation' => FormatHelper::formatToIndianCurrency(array_sum(array_column($leadsByStatus, 'total_valuation')))
            ],
            'leadsByStatus' => array_map(function ($status, $data) {
                return [
                    'name' => ucfirst(str_replace('_', ' ', $status)),
                    'count' => $data['count'],
                    'valuation' => FormatHelper::formatToIndianCurrency($data['total_valuation']),
                    'colorClass' => match($status) {
                        'personal_lead' => 'bg-blue-50 text-blue-800',
                        'authorized' => 'bg-cyan-50 text-cyan-800',
                        'login' => 'bg-amber-50 text-amber-800',
                        'approved' => 'bg-violet-50 text-violet-800',
                        'rejected' => 'bg-red-50 text-red-800',
                        'disbursed' => 'bg-emerald-50 text-emerald-800',
                        'future_lead' => 'bg-lime-50 text-lime-800',
                        default => 'bg-gray-50 text-gray-800',
                    },
                    'iconClass' => match($status) {
                        'personal_lead' => 'fas fa-user-plus',
                        'authorized' => 'fas fa-check-circle',
                        'login' => 'fas fa-sign-in-alt',
                        'approved' => 'fas fa-thumbs-up',
                        'rejected' => 'fas fa-times-circle',
                        'disbursed' => 'fas fa-money-bill-wave',
                        'future_lead' => 'fas fa-clock',
                        default => 'fas fa-info-circle',
                    }
                ];
            }, array_keys($leadsByStatus), $leadsByStatus),
        ];

        return response()->json($response);
    }

    // Rest of the controller methods remain unchanged
    public function filterCreditCard(Request $request)
    {
        // Validate request
        $request->validate([
            'credit_card_date_filter' => 'required|in:7_days,15_days,30_days,60_days,custom',
            'credit_card_start_date' => 'nullable|date|required_if:credit_card_date_filter,custom',
            'credit_card_end_date' => 'nullable|date|required_if:credit_card_date_filter,custom|after_or_equal:credit_card_start_date',
        ]);

        $creditCardDateFilter = $request->input('credit_card_date_filter', '30_days');
        $creditCardStartDate = $request->input('credit_card_start_date');
        $creditCardEndDate = $request->input('credit_card_end_date');

        // Credit card leads
        $creditCardLeadsQuery = Lead::query()
            ->select('id', 'name', 'email', 'dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id')
            ->with(['employee' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('lead_type', 'creditcard_loan');

        if ($creditCardDateFilter === 'custom' && $creditCardStartDate && $creditCardEndDate) {
            $creditCardLeadsQuery->whereBetween('created_at', [$creditCardStartDate, $creditCardEndDate]);
        } elseif ($creditCardDateFilter) {
            $days = match ($creditCardDateFilter) {
                '7_days' => 7,
                '15_days' => 15,
                '30_days' => 30,
                '60_days' => 60,
                default => 30,
            };
            $creditCardLeadsQuery->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        $creditCardLeads = $creditCardLeadsQuery->get()->map(function ($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'dob' => $lead->dob ? $lead->dob->format('Y-m-d') : null,
                'city' => $lead->city,
                'district' => $lead->district,
                'state' => $lead->state,
                'company_name' => $lead->company_name,
                'lead_amount' => FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0),
                'salary' => FormatHelper::formatToIndianCurrency($lead->salary ?? 0),
                'status' => $lead->status,
                'lead_type' => $lead->lead_type,
                'bank_name' => $lead->bank_name,
            ];
        })->toArray();

        return response()->json(['creditCardLeads' => $creditCardLeads]);
    }

    public function filterCharts(Request $request)
    {
        // Validate request
        $request->validate([
            'chart_date_filter' => 'required|in:7_days,15_days,30_days,custom',
            'chart_start_date' => 'nullable|date|required_if:chart_date_filter,custom',
            'chart_end_date' => 'nullable|date|required_if:chart_date_filter,custom|after_or_equal:chart_start_date',
            'operation_id' => 'nullable|exists:users,id',
            'team_lead_id' => 'nullable|exists:users,id',
            'employee_id' => 'nullable|exists:users,id',
        ]);

        $chartDateFilter = $request->input('chart_date_filter', '30_days');
        $chartStartDate = $request->input('chart_start_date');
        $chartEndDate = $request->input('chart_end_date');
        $operationId = $request->input('operation_id');
        $teamLeadId = $request->input('team_lead_id');
        $employeeId = $request->input('employee_id');

        // Base query for leads
        $baseLeadsQuery = Lead::query()->with(['employee' => function ($query) {
            $query->select('id', 'name', 'team_lead_id', 'created_by');
        }])->whereNotIn('lead_type', ['creditcard_loan']);

        if ($chartDateFilter === 'custom' && $chartStartDate && $chartEndDate) {
            $baseLeadsQuery->whereBetween('created_at', [$chartStartDate, $chartEndDate]);
        } elseif ($chartDateFilter) {
            $days = match ($chartDateFilter) {
                '7_days' => 7,
                '15_days' => 15,
                '30_days' => 30,
                default => 30,
            };
            $baseLeadsQuery->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        if ($operationId) {
            $baseLeadsQuery->whereHas('employee', function ($query) use ($operationId) {
                $query->whereIn('team_lead_id', User::where('designation', 'team_lead')
                    ->where('created_by', $operationId)
                    ->pluck('id'));
            });
        }

        if ($teamLeadId) {
            $baseLeadsQuery->whereHas('employee', function ($query) use ($teamLeadId) {
                $query->where('team_lead_id', $teamLeadId);
            });
        }

        if ($employeeId) {
            $baseLeadsQuery->where('employee_id', $employeeId);
        }

        // Leads per employee
        $leadsPerEmployee = (clone $baseLeadsQuery)
            ->select('employee_id', DB::raw('COUNT(*) as lead_count'))
            ->groupBy('employee_id')
            ->get()
            ->map(function ($item) {
                return ['name' => $item->employee->name ?? 'Unknown', 'lead_count' => $item->lead_count];
            })->toArray();

        // Lead status distribution
        $leadStatusDistribution = (clone $baseLeadsQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            })->toArray();

        return response()->json([
            'leadsPerEmployee' => $leadsPerEmployee,
            'leadStatusDistribution' => $leadStatusDistribution,
        ]);
    }

    public function filterTasks(Request $request)
    {
        // Validate request
        $request->validate([
            'task_date_filter' => 'required|in:7_days,15_days,30_days,60_days,custom',
            'task_start_date' => 'nullable|date|required_if:task_date_filter,custom',
            'task_end_date' => 'nullable|date|required_if:task_date_filter,custom|after_or_equal:task_start_date',
            'task_target_type' => 'nullable|in:individual,individual_teamlead,individual_operation',
            'task_operation_id' => 'nullable|exists:users,id',
            'task_team_lead_id' => 'nullable|exists:users,id',
            'task_employee_id' => 'nullable|exists:users,id',
        ]);

        $taskDateFilter = $request->input('task_date_filter', '30_days');
        $taskStartDate = $request->input('task_start_date');
        $taskEndDate = $request->input('task_end_date');
        $taskTargetType = $request->input('task_target_type', '');
        $taskOperationId = $request->input('task_operation_id', '');
        $taskTeamLeadId = $request->input('task_team_lead_id', '');
        $taskEmployeeId = $request->input('task_employee_id', '');

        // Tasks query
        $tasksQuery = Task::query()
            ->where('admin_id', auth()->id())
            ->with(['teamLead:id,name', 'assignedUsers:id,name'])
            ->select('id', 'title', 'target_type', 'priority', 'status', 'assigned_date', 'due_date', 'progress', 'team_lead_id');

        if ($taskDateFilter === 'custom' && $taskStartDate && $taskEndDate) {
            $tasksQuery->whereBetween('assigned_date', [$taskStartDate, $taskEndDate]);
        } elseif ($taskDateFilter) {
            $days = match ($taskDateFilter) {
                '7_days' => 7,
                '15_days' => 15,
                '30_days' => 30,
                '60_days' => 60,
                default => 30,
            };
            $tasksQuery->where('assigned_date', '>=', Carbon::now()->subDays($days));
        }

        if ($taskTargetType) {
            $tasksQuery->where('target_type', $taskTargetType);
        }

        if ($taskOperationId) {
            $tasksQuery->where('operations_id', $taskOperationId);
        }

        if ($taskTeamLeadId) {
            $tasksQuery->where('team_lead_id', $taskTeamLeadId);
        }

        if ($taskEmployeeId) {
            $tasksQuery->whereHas('assignedUsers', function ($query) use ($taskEmployeeId) {
                $query->where('users.id', $taskEmployeeId);
            });
        }

        $tasks = $tasksQuery->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'target_type' => ucfirst(str_replace('_', ' ', $task->target_type)),
                'priority' => $task->priority,
                'status' => $task->status,
                'progress' => $task->progress,
                'assigned_date' => $task->assigned_date ? $task->assigned_date->format('Y-m-d') : null,
                'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
            ];
        })->toArray();

        return response()->json(['tasks' => $tasks]);
    }

    public function filterAttendance(Request $request)
    {
        // Validate request
        $request->validate([
            'attendance_date' => 'required|date',
            'attendance_role_filter' => 'required|in:all,team_lead,employee,operation',
        ]);

        $attendanceDate = $request->input('attendance_date', Carbon::today()->toDateString());
        $attendanceRoleFilter = $request->input('attendance_role_filter', 'all');

        // Attendance query
        $attendanceQuery = Attendance::query()
            ->with(['employee:id,name,designation', 'teamLead:id,name,designation', 'operation:id,name,designation'])
            ->whereDate('date', $attendanceDate);

        if ($attendanceRoleFilter === 'team_lead') {
            $attendanceQuery->whereHas('teamLead', function ($query) {
                $query->where('designation', 'team_lead');
            });
        } elseif ($attendanceRoleFilter === 'employee') {
            $attendanceQuery->whereHas('employee', function ($query) {
                $query->where('designation', 'employee');
            });
        } elseif ($attendanceRoleFilter === 'operation') {
            $attendanceQuery->whereHas('operation', function ($query) {
                $query->where('designation', 'operations');
            });
        }

        $attendances = $attendanceQuery->get()->map(function ($attendance) {
            return [
                'name' => $attendance->employee->name ?? $attendance->teamLead->name ?? $attendance->operation->name ?? 'N/A',
                'designation' => $attendance->employee->designation ?? $attendance->teamLead->designation ?? $attendance->operation->designation ?? 'N/A',
                'check_in' => $attendance->check_in ? $attendance->check_in->format('H:i') : null,
                'check_out' => $attendance->check_out ? $attendance->check_out->format('H:i') : null,
                'check_in_location' => $attendance->check_in_location,
                'check_out_location' => $attendance->check_out_location,
                'notes' => $attendance->notes,
            ];
        })->toArray();

        return response()->json(['attendances' => $attendances]);
    }

    // [Unchanged methods: getTeamLeads, getEmployees, getDistricts, getCities, getLeadHistory, getTaskUsers]
    public function getTeamLeads(Request $request)
    {
        $operationId = $request->input('operation_id');
        $teamLeads = User::where('designation', 'team_lead')
            ->when($operationId, function ($query) use ($operationId) {
                return $query->where('created_by', $operationId);
            })
            ->select('id', 'name')
            ->get()
            ->toArray();
        return response()->json($teamLeads);
    }

    public function getEmployees(Request $request)
    {
        $teamLeadId = $request->input('team_lead_id');
        $employees = User::where('designation', 'employee')
            ->when($teamLeadId, function ($query) use ($teamLeadId) {
                return $query->where('team_lead_id', $teamLeadId);
            })
            ->select('id', 'name')
            ->get()
            ->toArray();
        return response()->json($employees);
    }

    public function getDistricts(Request $request)
    {
        $state = $request->input('state');
        $districts = District::where('district_status', 'Active')
            ->when($state, function ($query) use ($state) {
                return $query->whereIn('state_id', State::where('state_title', $state)
                    ->where('status', 'Active')
                    ->pluck('state_id'));
            })
            ->select('districtid', 'district_title')
            ->get()
            ->toArray();
        return response()->json($districts);
    }

    public function getCities(Request $request)
    {
        $district = $request->input('district');
        $cities = City::where('status', 'Active')
            ->when($district, function ($query) use ($district) {
                return $query->whereIn('districtid', District::where('district_title', $district)
                    ->where('district_status', 'Active')
                    ->pluck('districtid'));
            })
            ->select('id', 'name')
            ->get()
            ->toArray();
        return response()->json($cities);
    }

    public function getLeadHistory(Request $request, $leadId)
    {
        $lead = Lead::findOrFail($leadId);
        $histories = $lead->histories()
            ->with(['user:id,name', 'forwardedTo:id,name'])
            ->select('id', 'lead_id', 'user_id', 'action', 'status', 'forwarded_to', 'comments', 'created_at')
            ->get();

        return view('admin.partials.lead-history-modal', compact('lead', 'histories'));
    }

    public function getTaskUsers(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        $taskUsers = $task->assignedUsers()
            ->select('users.id', 'users.name', 'task_user.status', 'task_user.progress', 'task_user.message', 'task_user.completed_at')
            ->get();

        return view('admin.partials.task-users-modal', compact('task', 'taskUsers'));
    }


      public function leadAnalytics(Request $request)
    {
    $tableLeadType = $request->input('table_lead_type', '');
    $tableFromDate = $request->input('table_from_date');
    $tableToDate = $request->input('table_to_date');
    // Leads by status (excluding future_lead and creditcard_loan)
    $leadsQuery = Lead::query()->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
        ->whereNotIn('status', ['future_lead'])
        ->whereNotIn('lead_type', ['creditcard_loan']);

    if ($tableLeadType) {
        $leadsQuery->where('lead_type', $tableLeadType);
    }
    if ($tableFromDate && $tableToDate) {
        $leadsQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
    }

    $leadsByStatus = $leadsQuery->groupBy('status')->get()->mapWithKeys(function ($item) {
        return [$item->status => ['count' => $item->count, 'total_valuation' => $item->total_valuation ?? 0]];
    })->toArray();
    $allStatuses = ['personal_lead', 'authorized', 'login', 'approved', 'rejected', 'disbursed'];
    foreach ($allStatuses as $status) {
        if (!isset($leadsByStatus[$status])) {
            $leadsByStatus[$status] = ['count' => 0, 'total_valuation' => 0];
        }
    }
    $totalLeads = array_sum(array_column($leadsByStatus, 'count'));
    $totalValuation = array_sum(array_column($leadsByStatus, 'total_valuation'));

    // Current month leads based on expected_month or custom date range
    $currentMonthQuery = Lead::query()->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
        ->whereNotIn('lead_type', ['creditcard_loan']);

    if ($tableFromDate && $tableToDate) {
        $currentMonthQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
    } else {
        $currentMonth = Carbon::now()->format('F'); // Returns "July"
        $currentMonthQuery->where('expected_month', $currentMonth); // Default to July if no custom dates
    }

    if ($tableLeadType) {
        $currentMonthQuery->where('lead_type', $tableLeadType);
    }

    $currentMonthLeads = $currentMonthQuery->groupBy('status')->get()->mapWithKeys(function ($item) {
        return [$item->status => ['count' => $item->count, 'total_valuation' => $item->total_valuation ?? 0]];
    })->toArray();
    foreach (['login', 'approved', 'rejected', 'disbursed'] as $status) {
        $leadsByStatus[$status] = $currentMonthLeads[$status] ?? ['count' => 0, 'total_valuation' => 0];
    }

    // Future leads query
    $futureLeadsQuery = Lead::where('status', 'future_lead');

    if ($tableLeadType) {
        $futureLeadsQuery->where('lead_type', $tableLeadType);
    }
    if ($tableFromDate && $tableToDate) {
        $futureLeadsQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
    } else {
        $futureLeadsQuery->where('expected_month', $currentMonth); // Default to July if no custom dates
    }

    $futureLeadCount = $futureLeadsQuery->count();
 // Today's leads (created or status changed today, excluding creditcard_loan)
$createdTodayLeadIds = Lead::whereDate('created_at', Carbon::today())
    ->pluck('id')
    ->toArray();

$statusChangedLeadIds = LeadHistory::where('action', 'status_changed')
    ->whereDate('created_at', Carbon::today())
    ->pluck('lead_id')
    ->toArray();

$allRelevantLeadIds = array_unique(array_merge($createdTodayLeadIds, $statusChangedLeadIds));

$todaysLeadsByStatus = Lead::whereIn('id', $allRelevantLeadIds)
    ->whereNotIn('lead_type', ['creditcard_loan'])
    ->when($tableLeadType, fn($q) => $q->where('lead_type', $tableLeadType))
    ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
    ->groupBy('status')
    ->get()
    ->mapWithKeys(function ($item) {
        $colorClass = match($item->status) {
            'personal_lead' => 'bg-blue-100 text-blue-800',
            'authorized' => 'bg-emerald-100 text-emerald-800',
            'login' => 'bg-amber-100 text-amber-800',
            'approved' => 'bg-violet-100 text-violet-800',
            'rejected' => 'bg-red-100 text-red-800',
            'disbursed' => 'bg-cyan-100 text-cyan-800',
            'future_lead' => 'bg-lime-100 text-lime-800',
            default => 'bg-gray-100 text-gray-800',
        };
        return [$item->status => [
            'count' => $item->count,
            'total_valuation' => $item->total_valuation ?? 0,
            'colorClass' => $colorClass
        ]];
    })->toArray();

$allStatuses = ['personal_lead', 'authorized', 'login', 'approved', 'rejected', 'disbursed', 'future_lead'];
foreach ($allStatuses as $status) {
    if (!isset($todaysLeadsByStatus[$status])) {
        $todaysLeadsByStatus[$status] = [
            'count' => 0,
            'total_valuation' => 0,
            'colorClass' => match($status) {
                'personal_lead' => 'bg-blue-100 text-blue-800',
                'authorized' => 'bg-emerald-100 text-emerald-800',
                'login' => 'bg-amber-100 text-amber-800',
                'approved' => 'bg-violet-100 text-violet-800',
                'rejected' => 'bg-red-100 text-red-800',
                'disbursed' => 'bg-cyan-100 text-cyan-800',
                'future_lead' => 'bg-lime-100 text-lime-800',
                default => 'bg-gray-100 text-gray-800',
            }
        ];
    }
}
        // credit card leads
        $creditCardDateFilter = $request->input('credit_card_date_filter', '30_days');
        $creditCardStartDate = $request->input('credit_card_start_date');
        $creditCardEndDate = $request->input('credit_card_end_date');

        $creditCardLeadsQuery = Lead::query()
            ->select('id', 'name', 'email', 'dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id')
            ->with(['employee' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('lead_type', 'creditcard_loan');

        if ($creditCardDateFilter === 'custom' && $creditCardStartDate && $creditCardEndDate) {
            $creditCardLeadsQuery->whereBetween('created_at', [$creditCardStartDate, $creditCardEndDate]);
        } elseif ($creditCardDateFilter) {
            $days = match ($creditCardDateFilter) {
                '7_days' => 7,
                '15_days' => 15,
                '30_days' => 30,
                '60_days' => 60,
                default => 30,
            };
            $creditCardLeadsQuery->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        $creditCardLeads = $creditCardLeadsQuery->get();

        $chartDateFilter = $request->input('chart_date_filter', '30_days');
        $chartStartDate = $request->input('chart_start_date');
        $chartEndDate = $request->input('chart_end_date');
        $operationId = $request->input('operation_id');
        $teamLeadId = $request->input('team_lead_id');
        $employeeId = $request->input('employee_id');

        $baseLeadsQuery = Lead::query()->with(['employee' => function ($query) {
            $query->select('id', 'name', 'team_lead_id', 'created_by');
        }])->whereNotIn('lead_type', ['creditcard_loan']);

        if ($chartDateFilter === 'custom' && $chartStartDate && $chartEndDate) {
            $baseLeadsQuery->whereBetween('created_at', [$chartStartDate, $chartEndDate]);
        } elseif ($chartDateFilter) {
            $days = match ($chartDateFilter) {
                '7_days' => 7,
                '15_days' => 15,
                '30_days' => 30,
                default => 30,
            };
            $baseLeadsQuery->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        if ($operationId) {
            $baseLeadsQuery->whereHas('employee', function ($query) use ($operationId) {
                $query->whereIn('team_lead_id', User::where('designation', 'team_lead')
                    ->where('created_by', $operationId)
                    ->pluck('id'));
            });
        }

        if ($teamLeadId) {
            $baseLeadsQuery->whereHas('employee', function ($query) use ($teamLeadId) {
                $query->where('team_lead_id', $teamLeadId);
            });
        }

        if ($employeeId) {
            $baseLeadsQuery->where('employee_id', $employeeId);
        }

        $leadsPerEmployee = (clone $baseLeadsQuery)
            ->select('employee_id', DB::raw('COUNT(*) as lead_count'))
            ->groupBy('employee_id')
            ->get()
            ->map(function ($item) {
                return ['name' => $item->employee->name ?? 'Unknown', 'lead_count' => $item->lead_count];
            });

        $leadStatusDistribution = (clone $baseLeadsQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            })->toArray();

        $operations = User::where('designation', 'operations')->select('id', 'name')->get();
        $teamLeads = User::where('designation', 'team_lead')
            ->when($operationId, function ($query) use ($operationId) {
                return $query->where('created_by', $operationId);
            })
            ->select('id', 'name')
            ->get();
        $employees = User::where('designation', 'employee')
            ->when($teamLeadId, function ($query) use ($teamLeadId) {
                return $query->where('team_lead_id', $teamLeadId);
            })
            ->select('id', 'name')
            ->get();

        $search = $request->input('search', '');
        $status = $request->input('status', '');
        $state = $request->input('state', '');
        $district = $request->input('district', '');
        $city = $request->input('city', '');
        $leadType = $request->input('lead_type', '');
        $leadOperationId = $request->input('lead_operation_id', '');
        $leadTeamLeadId = $request->input('lead_team_lead_id', '');
        $leadEmployeeId = $request->input('lead_employee_id', '');
        $minAmount = $request->input('min_amount', '');
        $maxAmount = $request->input('max_amount', '');
        $leadDateFilter = $request->input('lead_date_filter', '');
        $leadStartDate = $request->input('lead_start_date', '');
        $leadEndDate = $request->input('lead_end_date', '');

        $leadsQuery = Lead::query()
            ->select('id', 'name', 'email', 'dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id')
            ->with(['employee' => function ($query) {
                $query->select('id', 'name');
            }])
            ->whereNotIn('lead_type', ['creditcard_loan']);

        if ($search) {
            $leadsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $leadsQuery->where('status', $status);
        }

        if ($state) {
            $leadsQuery->where('state', $state);
        }

        if ($district) {
            $leadsQuery->where('district', $district);
        }

        if ($city) {
            $leadsQuery->where('city', $city);
        }

        if ($leadType) {
            $leadsQuery->where('lead_type', $leadType);
        }

        if ($leadOperationId) {
            $leadsQuery->whereHas('employee', function ($query) use ($leadOperationId) {
                $query->whereIn('team_lead_id', User::where('designation', 'team_lead')
                    ->where('created_by', $leadOperationId)
                    ->pluck('id'));
            });
        }

        if ($leadTeamLeadId) {
            $leadsQuery->whereHas('employee', function ($query) use ($leadTeamLeadId) {
                $query->where('team_lead_id', $leadTeamLeadId);
            });
        }

        if ($leadEmployeeId) {
            $leadsQuery->where('employee_id', $leadEmployeeId);
        }

        if ($minAmount) {
            $leadsQuery->where('lead_amount', '>=', $minAmount);
        }

        if ($maxAmount) {
            $leadsQuery->where('lead_amount', '<=', $maxAmount);
        }

        if ($leadDateFilter === 'custom' && $leadStartDate && $leadEndDate) {
            $leadsQuery->whereBetween('created_at', [$leadStartDate, $leadEndDate]);
        } elseif ($leadDateFilter) {
            $days = match ($leadDateFilter) {
                '7_days' => 7,
                '15_days' => 15,
                '30_days' => 30,
                '60_days' => 60,
                default => null,
            };
            if ($days) {
                $leadsQuery->where('created_at', '>=', Carbon::now()->subDays($days));
            }
        }

        $leads = $leadsQuery->get();

        $statuses = Lead::select('status')->distinct()->pluck('status')->toArray();
        $states = State::where('status', 'Active')->select('state_id', 'state_title')->get();
        $districts = District::where('district_status', 'Active')
            ->when($state, function ($query) use ($state) {
                return $query->whereIn('state_id', State::where('state_title', $state)
                    ->where('status', 'Active')
                    ->pluck('state_id'));
            })
            ->select('districtid', 'district_title')
            ->get();
        $cities = City::where('status', 'Active')
            ->when($district, function ($query) use ($district) {
                return $query->whereIn('districtid', District::where('district_title', $district)
                    ->where('district_status', 'Active')
                    ->pluck('districtid'));
            })
            ->select('id', 'name')
            ->get();
        $leadTypes = Lead::select('lead_type')->distinct()->whereNotIn('lead_type', ['creditcard_loan'])->pluck('lead_type')->filter()->toArray();

        $taskDateFilter = $request->input('task_date_filter', '30_days');
        $taskStartDate = $request->input('task_start_date');
        $taskEndDate = $request->input('task_end_date');
        $taskTargetType = $request->input('task_target_type', '');
        $taskOperationId = $request->input('task_operation_id', '');
        $taskTeamLeadId = $request->input('task_team_lead_id', '');
        $taskEmployeeId = $request->input('task_employee_id', '');

        $tasksQuery = Task::query()
            ->where('admin_id', auth()->id())
            ->with(['teamLead:id,name', 'assignedUsers:id,name'])
            ->select('id', 'title', 'target_type', 'priority', 'status', 'assigned_date', 'due_date', 'progress', 'team_lead_id');

        if ($taskDateFilter === 'custom' && $taskStartDate && $taskEndDate) {
            $tasksQuery->whereBetween('assigned_date', [$taskStartDate, $taskEndDate]);
        } elseif ($taskDateFilter) {
            $days = match ($taskDateFilter) {
                '7_days' => 7,
                '15_days' => 15,
                '30_days' => 30,
                '60_days' => 60,
                default => 30,
            };
            $tasksQuery->where('assigned_date', '>=', Carbon::now()->subDays($days));
        }

        if ($taskTargetType) {
            $tasksQuery->where('target_type', $taskTargetType);
        }

        if ($taskOperationId) {
            $tasksQuery->where('operations_id', $taskOperationId);
        }

        if ($taskTeamLeadId) {
            $tasksQuery->where('team_lead_id', $teamLeadId);
        }

        if ($taskEmployeeId) {
            $tasksQuery->whereHas('assignedUsers', function ($query) use ($taskEmployeeId) {
                $query->where('users.id', $taskEmployeeId);
            });
        }

        $tasks = $tasksQuery->get();

        $taskTargetTypes = ['all', 'individual', 'individual_teamlead', 'individual_operation'];

        $attendanceDate = $request->input('attendance_date', Carbon::today()->toDateString());
        $attendanceRoleFilter = $request->input('attendance_role_filter', 'all');

        $attendanceQuery = Attendance::query()
            ->with(['employee:id,name,designation', 'teamLead:id,name,designation', 'operation:id,name,designation'])
            ->whereDate('date', $attendanceDate);

        if ($attendanceRoleFilter === 'team_lead') {
            $attendanceQuery->whereHas('teamLead', function ($query) {
                $query->where('designation', 'team_lead');
            });
        } elseif ($attendanceRoleFilter === 'employee') {
            $attendanceQuery->whereHas('employee', function ($query) {
                $query->where('designation', 'employee');
            });
        } elseif ($attendanceRoleFilter === 'operation') {
            $attendanceQuery->whereHas('operation', function ($query) {
                $query->where('designation', 'operations');
            });
        }

        $attendances = $attendanceQuery->get();

        $allUsers = User::select('id', 'name', 'designation')->get();
        $teamLeads = $allUsers->where('designation', 'team_lead');
        $employees = $allUsers->where('designation', 'employee');
        $operations = $allUsers->where('designation', 'operations');

        return view('admin.leads-analytics', compact(
            'totalLeads', 'totalValuation',
            'leadsByStatus','todaysLeadsByStatus','leadsPerEmployee', 'leadStatusDistribution', 'operations', 'teamLeads', 'employees',
            'tableFromDate', 'tableToDate', 'tableLeadType',
            'creditCardLeads', 'creditCardDateFilter', 'creditCardStartDate', 'creditCardEndDate',
            'chartDateFilter', 'chartStartDate', 'chartEndDate',
            'operationId', 'teamLeadId', 'employeeId',
            'leads', 'statuses', 'states', 'districts', 'cities', 'leadTypes',
            'search', 'status', 'state', 'district', 'city', 'leadType',
            'leadOperationId', 'leadTeamLeadId', 'leadEmployeeId', 'minAmount', 'maxAmount',
            'leadDateFilter', 'leadStartDate', 'leadEndDate',
            'tasks', 'taskDateFilter', 'taskStartDate', 'taskEndDate',
            'taskTargetType', 'taskOperationId', 'taskTeamLeadId', 'taskEmployeeId', 'taskTargetTypes',
            'attendances', 'attendanceDate', 'attendanceRoleFilter', 'allUsers', 'teamLeads', 'employees', 'operations'
        ));
    }

    //     public function getChartsData(Request $request)
    // {
    //     $data = $this->prepareChartsData($request);
    //     return response()->json($data);
    // }

    //     protected function prepareChartsData(Request $request)
    // {
    //     $chartDateFilter = $request->input('charts_date_filter', '30_days');
    //     $chartStartDate = $request->input('charts_start_date');
    //     $chartEndDate = $request->input('charts_end_date');
    //     $operationId = $request->input('charts_operation_id');
    //     $teamLeadId = $request->input('charts_team_lead_id');
    //     $employeeId = $request->input('charts_employee_id');

    //     $baseLeadsQuery = Lead::query()->with(['employee' => fn($query) => $query->select('id', 'name', 'team_lead_id', 'created_by')]);

    //     if ($chartDateFilter === 'custom' && $chartStartDate && $chartEndDate) {
    //         $baseLeadsQuery->whereBetween('created_at', [$chartStartDate, $chartEndDate]);
    //     } elseif ($chartDateFilter) {
    //         $days = match ($chartDateFilter) {
    //             '7_days' => 7, '15_days' => 15, '30_days' => 30, default => 30,
    //         };
    //         $baseLeadsQuery->where('created_at', '>=', Carbon::now()->subDays($days));
    //     }

    //     if ($operationId) {
    //         $baseLeadsQuery->whereHas('employee', fn($query) => $query->whereIn('team_lead_id', User::where('designation', 'team_lead')
    //             ->where('created_by', $operationId)->pluck('id')));
    //     }
    //     if ($teamLeadId) {
    //         $baseLeadsQuery->whereHas('employee', fn($query) => $query->where('team_lead_id', $teamLeadId));
    //     }
    //     if ($employeeId) {
    //         $baseLeadsQuery->where('employee_id', $employeeId);
    //     }

    //     $leadsPerEmployee = (clone $baseLeadsQuery)
    //         ->select('employee_id', DB::raw('COUNT(*) as lead_count'))
    //         ->groupBy('employee_id')
    //         ->limit(10)
    //         ->get()
    //         ->map(fn($item) => ['name' => $item->employee->name ?? 'Unknown', 'lead_count' => $item->lead_count]);

    //     $leadStatusDistribution = (clone $baseLeadsQuery)
    //         ->select('status', DB::raw('COUNT(*) as count'))
    //         ->groupBy('status')
    //         ->get()
    //         ->mapWithKeys(fn($item) => [$item->status => $item->count])
    //         ->toArray();

    //     $operations = Cache::remember('operations_' . $operationId, self::CACHE_TTL, fn() => User::where('designation', 'operations')->select('id', 'name')->get());
    //     $teamLeads = Cache::remember('team_leads_' . $operationId, self::CACHE_TTL, fn() => User::where('designation', 'team_lead')
    //         ->when($operationId, fn($query) => $query->where('created_by', $operationId))
    //         ->select('id', 'name')->get());
    //     $employees = Cache::remember('employees_' . $teamLeadId, self::CACHE_TTL, fn() => User::where('designation', 'employee')
    //         ->when($teamLeadId, fn($query) => $query->where('team_lead_id', $teamLeadId))
    //         ->select('id', 'name')->get());

    //     return compact('leadsPerEmployee', 'leadStatusDistribution', 'operations', 'teamLeads', 'employees');
    // }



public function updateAndExportMonths(Request $request)
{
    $now          = Carbon::now();
    $currentMonth = $now->format('F');
    $currentYear  = $now->year;

    $affectedLeads = [];

    // Month mapping (string → number)
    $monthMap = [
        'January' => 1, 'February' => 2, 'March' => 3,
        'April' => 4, 'May' => 5, 'June' => 6,
        'July' => 7, 'August' => 8, 'September' => 9,
        'October' => 10, 'November' => 11, 'December' => 12,
    ];

    try {
        DB::beginTransaction();

        $leads = Lead::whereIn('status', ['personal_lead','authorized','login','approved'])
            ->whereNotIn('lead_type', ['creditcard_loan'])
            ->where('status', '!=', 'future_lead')
            ->where('expected_month', '!=', $currentMonth)
            ->with('teamLead')
            ->get();

        foreach ($leads as $lead) {

            // Safety: invalid month
            if (!isset($monthMap[$lead->expected_month])) {
                continue;
            }

            // ✅ Use lead created year
            $leadYear = $lead->created_at->year;
            $monthNum = $monthMap[$lead->expected_month];

            // Build expected date from lead year
            $expectedDate = Carbon::createFromDate(
                $leadYear,
                $monthNum,
                1
            )->endOfMonth();

            // ✅ Only forward if truly in the past
            if (!$expectedDate->lt($now)) {
                continue;
            }

            // 🔒 Prevent duplicate history creation
            $alreadyUpdated = LeadHistory::where('lead_id', $lead->id)
                ->where('action', 'month_updated')
                ->whereDate('created_at', $now->toDateString())
                ->exists();

            if ($alreadyUpdated) {
                continue;
            }

            $oldMonth = $lead->expected_month;

            $lead->update([
                'expected_month' => $currentMonth,
            ]);

            LeadHistory::create([
                'lead_id'  => $lead->id,
                'user_id'  => auth()->id(),
                'action'   => 'month_updated',
                'status'   => $lead->status,
                'comments' => 'Expected month auto-forwarded',
            ]);

            $affectedLeads[] = [
                'lead'          => $lead,
                'old_month'     => $oldMonth,
                'current_month' => $currentMonth,
            ];
        }

        DB::commit();

        if (count($affectedLeads) > 0) {
            event(new LiveDashboardUpdated(auth()->id()));
        }

        // 🔒 HARD STOP → no update = no export
        if (count($affectedLeads) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No leads needed updating',
            ], 200);
        }

        // 🔒 JSON response only
        if ($request->expectsJson() || !$request->boolean('export')) {
            return response()->json([
                'success'        => true,
                'updated_count'  => count($affectedLeads),
                'message'        => 'Leads updated successfully',
            ], 200);
        }

        // ================= CSV EXPORT =================

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=monthly_updates_' . now()->format('YmdHis') . '.csv',
        ];

        $callback = function () use ($affectedLeads) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Name',
                'Company',
                'Old Month',
                'New Month',
                'Status',
                'Lead Type',
                'Team Lead',
                'Updated At',
            ]);

            foreach ($affectedLeads as $item) {
                $lead = $item['lead'];

                fputcsv($file, [
                    $lead->id,
                    $lead->client_name ?? $lead->name,
                    $lead->company_name ?? $lead->company,
                    $item['old_month'],
                    $item['current_month'],
                    ucfirst(str_replace('_', ' ', $lead->status)),
                    $lead->lead_type,
                    optional($lead->teamLead)->name,
                    now()->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again.',
        ], 500);
    }
}

}
