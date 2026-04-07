<?php

namespace App\Http\Controllers\TLController;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadForwardedHistory;
use App\Models\Notification;
use App\Models\SalarySlip;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TLDashboardController extends Controller
{
public function dashboardStats(Request $request)
{
    $user = auth()->user(); // Make sure this is a team lead
    $tableLeadType = $request->input('table_lead_type', '');
    $tableFromDate = $request->input('table_from_date');
    $tableToDate = $request->input('table_to_date');
    $tableMonth = $request->input('table_month', '');

    // Employees under this team lead
    $employeeIds = User::where('team_lead_id', $user->id)
        ->pluck('id')
        ->toArray();

    $leadsQuery = Lead::query()
        ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
        ->whereIn('employee_id', $employeeIds)
        ->whereNotIn('lead_type', ['creditcard_loan'])
        ->whereNotIn('status', ['future_lead']);

    if ($tableLeadType) {
        $leadsQuery->where('lead_type', $tableLeadType);
    }

    if ($tableMonth) {
        $leadsQuery->where('expected_month', $tableMonth);
    } elseif ($tableFromDate && $tableToDate) {
        $leadsQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
    }

    $leadsByStatus = $leadsQuery->groupBy('status')->get()->mapWithKeys(function ($item) {
        return [$item->status => ['count' => $item->count, 'total_valuation' => $item->total_valuation ?? 0]];
    })->toArray();

    foreach (['personal_lead', 'authorized', 'login', 'approved', 'rejected', 'disbursed'] as $status) {
        if (!isset($leadsByStatus[$status])) {
            $leadsByStatus[$status] = ['count' => 0, 'total_valuation' => 0];
        }
    }

    $totalLeads = array_sum(array_column($leadsByStatus, 'count'));
    $totalValuation = array_sum(array_column($leadsByStatus, 'total_valuation'));

   // Current month leads based on expected_month or custom date range
$currentMonthQuery = Lead::query()
    ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
    ->whereIn('employee_id', $employeeIds)
    ->whereNotIn('lead_type', ['creditcard_loan'])
    ->whereNotIn('status', ['future_lead']);

// Log the initial query setup and employee IDs
Log::info('Current Month Leads Query Initialized', [
    'employee_ids' => $employeeIds,
    'lead_type_excluded' => ['creditcard_loan'],
    'status_excluded' => ['future_lead']
]);

if ($tableMonth) {
    $currentMonthQuery->where('expected_month', $tableMonth);
    Log::info('Applying expected_month filter', ['tableMonth' => $tableMonth]);
} elseif ($tableFromDate && $tableToDate) {
    try {
        $fromDate = Carbon::parse($tableFromDate)->startOfDay();
        $toDate = Carbon::parse($tableToDate)->endOfDay();
        $currentMonthQuery->whereBetween('created_at', [$fromDate, $toDate]);
        Log::info('Applying custom date range filter', [
            'tableFromDate' => $tableFromDate,
            'tableToDate' => $tableToDate,
            'parsedFromDate' => $fromDate->toDateTimeString(),
            'parsedToDate' => $toDate->toDateTimeString()
        ]);
    } catch (\Exception $e) {
        Log::error('Error parsing custom date range', [
            'tableFromDate' => $tableFromDate,
            'tableToDate' => $tableToDate,
            'error' => $e->getMessage()
        ]);
    }
} else {
    $currentMonth = Carbon::now()->format('F');
    $currentMonthQuery->where('expected_month', $currentMonth);
    Log::info('Applying default current month filter', [
        'currentMonth' => $currentMonth,
        'date' => Carbon::now()->toDateString()
    ]);
}

if ($tableLeadType) {
    $currentMonthQuery->where('lead_type', $tableLeadType);
    Log::info('Applying lead type filter', ['tableLeadType' => $tableLeadType]);
}

// Log the final SQL query and bindings for debugging
Log::debug('Current Month Leads Query SQL', [
    'sql' => $currentMonthQuery->toSql(),
    'bindings' => $currentMonthQuery->getBindings()
]);

$currentMonthLeads = $currentMonthQuery->groupBy('status')->get()->mapWithKeys(function ($item) {
    return [$item->status => ['count' => $item->count, 'total_valuation' => $item->total_valuation ?? 0]];
})->toArray();

// Log the results of the query
Log::info('Current Month Leads Results', [
    'leads' => $currentMonthLeads,
    'total_count' => array_sum(array_column($currentMonthLeads, 'count')),
    'total_valuation' => array_sum(array_column($currentMonthLeads, 'total_valuation'))
]);

foreach (['login', 'approved', 'rejected', 'disbursed'] as $status) {
    $leadsByStatus[$status] = $currentMonthLeads[$status] ?? ['count' => 0, 'total_valuation' => 0];
    Log::info('Updating leadsByStatus for status', [
        'status' => $status,
        'data' => $leadsByStatus[$status]
    ]);
}



    // Future Leads
    $futureLeadsQuery = Lead::query()
        ->where('status', 'future_lead')
        ->whereIn('employee_id', $employeeIds);

    if ($tableLeadType) {
        $futureLeadsQuery->where('lead_type', $tableLeadType);
    }

    elseif ($tableFromDate && $tableToDate) {
        $futureLeadsQuery->whereBetween('created_at', [$tableFromDate, $tableToDate]);
    }else{
        $futureLeadsQuery->whereNot('lead_type','creditcard_loan');
    }

    $futureLeadCount = $futureLeadsQuery->count();
    $leadsByStatus['future_lead'] = ['count' => $futureLeadCount, 'total_valuation' => 0];

    $leadTypes = Lead::select('lead_type')->distinct()->whereNotIn('lead_type', ['creditcard_loan'])->pluck('lead_type')->filter()->toArray();

    return view('TeamLead.dashboard', compact(
        'leadsByStatus',
        'totalLeads',
        'totalValuation',
        'tableFromDate',
        'tableToDate',
        'tableLeadType',
        'tableMonth',
        'leadTypes'
    ));
}


public function leadsByStatus(Request $request, $status)
{
    $userId = auth()->id();
    $search = $request->input('search');
    $leadType = $request->input('lead_type');
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $month = $request->input('month');
    $executiveFilter = $request->input('executive'); // New filter parameter

     // New filter parameters
    $nameFilter = $request->input('name');
    $phoneFilter = $request->input('phone');
    $loanAccountFilter = $request->input('loan_account');
    $companyFilter = $request->input('company');
    $loanAmountFilter = $request->input('loan_amount');
    $statusFilter = $request->input('status_filter');
    $leadTypeFilter = $request->input('lead_type_filter');
    $bankFilter = $request->input('bank');

    $loanAmountRange = $request->input('loan_amount_range');



    if (!$userId) {
        abort(403, 'Unauthorized access');
    }

    // Employees under this team lead
    $employeeIds = User::where('designation', 'employee')
        ->where('team_lead_id', $userId)
        ->pluck('id');

    if ($employeeIds->isEmpty()) {
        $leads = collect();
        $leadsData = [];
        $title = $status === 'total' ? 'All Leads' : ucfirst(str_replace('_', ' ', $status)) . ' Leads';
        $executives = collect(); // Empty collection for executives
        return view('TeamLead.lead_by_status', compact('leads', 'leadsData', 'search', 'title', 'status', 'executives', 'executiveFilter'));
    }

    // Get executives for dropdown - employees under this team lead who have leads
    $executives = User::where('designation', 'employee')
        ->where('team_lead_id', $userId)
        ->whereHas('leads')
        ->orderBy('name')
        ->get(['id', 'name']);

    // Start building the lead query
    $leadQuery = Lead::with(['employee', 'teamLead'])
        ->whereIn('employee_id', $employeeIds)
        ->whereNotIn('lead_type', ['creditcard_loan']);


    // Apply executive filter if selected
    if ($executiveFilter) {
        $leadQuery->where('employee_id', $executiveFilter);
    }

    // Apply other filters
    if ($leadType) {
        $leadQuery->where('lead_type', $leadType);
    }

    if ($month && $status !== 'future_lead') {
        $leadQuery->where('expected_month', $month);
    } elseif ($fromDate && $toDate) {
        $leadQuery->whereBetween('created_at', [$fromDate, $toDate]);
    } else {
        $statusesWithCurrentMonthFilter = ['login', 'approved', 'rejected', 'disbursed'];
        if (in_array($status, $statusesWithCurrentMonthFilter)) {
            $currentMonth = Carbon::now()->format('F');
            $leadQuery->where('expected_month', $currentMonth);
        }
    }

   // Special handling for future leads
    if ($status === 'future_lead') {
        $leadQuery->where('status', 'future_lead');
    } else {
        // For all other statuses, exclude future leads
        $leadQuery->whereNotIn('status', ['future_lead']);

        // Apply status logic for non-future leads
        if ($status === 'total') {
            // no status filter needed
        } elseif ($status === 'pending') {
            $leadQuery->whereNotIn('status', ['authorized', 'login', 'approved', 'rejected', 'disbursed']);
        } else {
            $leadQuery->where('status', $status);
        }
    }

     // Apply the new filters
    if ($nameFilter) {
        $leadQuery->where('name', 'like', '%' . $nameFilter . '%');
    }

    if ($phoneFilter) {
        $leadQuery->where('phone', 'like', '%' . $phoneFilter . '%');
    }

    if ($loanAccountFilter) {
        $leadQuery->where('loan_account_number', 'like', '%' . $loanAccountFilter . '%');
    }

    if ($companyFilter) {
        $leadQuery->where('company_name', 'like', '%' . $companyFilter . '%');
    }

    if ($loanAmountFilter) {
        $leadQuery->where('lead_amount', $loanAmountFilter);
    }

    if ($statusFilter) {
        $leadQuery->where('status', $statusFilter);
    }

    if ($leadTypeFilter) {
        $leadQuery->where('lead_type', $leadTypeFilter);
    }

    if ($bankFilter) {
        $leadQuery->where('bank_name', 'like', '%' . $bankFilter . '%');
    }

    if ($loanAmountRange) {
    if ($loanAmountRange === '1-1000') {
        $leadQuery->whereBetween('lead_amount', [1, 1000]);
    } elseif ($loanAmountRange === '10000-100000') {
        $leadQuery->whereBetween('lead_amount', [10000, 100000]);
    } elseif ($loanAmountRange === '100000-1000000') {
        $leadQuery->whereBetween('lead_amount', [100000, 1000000]);
    } elseif ($loanAmountRange === '1000000+') {
        $leadQuery->where('lead_amount', '>', 1000000);
    }
}


    // Search logic
    if ($search) {
        $leadQuery->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('state', 'like', "%{$search}%")
                ->orWhere('district', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%");
        });
    }
   
    
    // Get results
    $leads = $leadQuery->orderBy('updated_at', 'desc')->get();

     $leadsData = $leads->map(function ($lead) {
        // Format documents for the lead
        $documents = $lead->documents->map(function ($document) {
            return [
                'id' => $document->id,
                'name' => $document->name,
                'type' => $document->type,
                'description' => $document->description,
                'filepath' => $document->pivot->filepath,
                'uploaded_at' => $document->pivot->uploaded_at,
                'url' => asset('storage/' . $document->pivot->filepath)
            ];
        });
         $followUps = FollowUp::where('lead_id', $lead->id)
    ->with('user:id,name') // get user info
    ->orderBy('timestamp', 'desc')
    ->get(['id', 'user_id', 'message', 'recording_path', 'timestamp']);


        return [
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email ?? '',
            'phone' => $lead->phone ?? '',
            'loan_account_number' => $lead->loan_account_number ?? '',
            'dob' => $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('Y-m-d') : null,
            'city' => $lead->city ?? '',
            'district' => $lead->district ?? '',
            'state' => $lead->state ?? '',
            'company' => $lead->company_name ?? '',
            'bank_name' => $lead->bank_name ?? '',
            'amount' => $lead->lead_amount,
            'salary' => $lead->salary ?? '',
            'status' => $lead->status,
            'expected_month' => $lead->expected_month ?? '',
            'lead_type' => $lead->lead_type ?? '',
            'turnover_amount' => $lead->turnover_amount ?? '',
            'voice_recording' => $lead->voice_recording ?? '',
            'employee_name' => $lead->employee?->name ?? '',
            'team_lead_name' => $lead->teamLead?->name ?? '',
            'team_lead_assigned' => !is_null($lead->team_lead_id),
            'reason' => $lead->reason ?? '',
            'documents' => $documents, // Add documents array
             'followUps' => $followUps
        ];
    })->toArray();

    if ($status === 'total') {
        $title = 'All Leads';
    } elseif ($status === 'personal_lead') {
        $title = 'Personal Leads';
    } else {
        $title = ucfirst(str_replace('_', ' ', $status)) . ' Leads';
    }

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

    return view('TeamLead.lead_by_status', compact(
        'leads',
        'leadsData',
        'search',
        'title',
        'status',
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
        'banksName',
        'nameFilter',
        'phoneFilter',
        'loanAccountFilter',
        'companyFilter',
        'loanAmountFilter',
        'statusFilter',
        'leadTypeFilter',
        'bankFilter',
        'formattedLeadTypes',
        'leadType',
        'fromDate',
        'toDate',
        'month',
        'loanAmountRange'
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

    public function indexTeams()
    {

  $employees = User::withTrashed() // 👈 this is the important part
        ->where('team_lead_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

        // Count total and active employees
   $totalEmployees = User::withTrashed()
    ->where('team_lead_id', auth()->id())
    ->count();

$activeEmployees = User::where('team_lead_id', auth()->id())
    ->whereNull('deleted_at')
    ->count();




        return view('TeamLead.teams.index', compact('employees','totalEmployees','activeEmployees'));
    }

//       public function indexReports()
// {

//       $today = Carbon::today()->toDateString();

//     $attendances = Attendance::whereDate('date', $today)
//         ->select(
//             'id',
//             'employee_id',
//             'date',
//             'check_in',
//             'check_out',
//             'check_in_location',
//             'check_out_location',
//             'check_in_coordinates',
//             'check_out_coordinates',
//             'checkin_image',
//             'checkout_image',
//             'notes',
//             'is_within_geofence',
//             'created_at'
//         )
//         ->orderBy('check_in', 'asc')
//         ->get();

//     $stats = [
//         'total_leads' => Lead::count(),
//         'authorized_leads' => Lead::where('status', 'authorized')->count(),
//         'login_leads' => Lead::where('status', 'login')->count(),
//         'approved_leads' => Lead::where('status', 'approved')->count(),
//         'disbursed_leads' => Lead::where('status', 'disbursed')->count(),
//         'rejected_leads' => Lead::where('status', 'rejected')->count(),
//         'active_employees' => User::where('designation', 'employee')->whereNull('deleted_at')->count(), // If soft deletes used
//         'personal_leads' => Lead::where('status', 'personal_lead')->count(),
//     ];

//     return view('TeamLead.reports.index', compact('stats','attendances'));
// }

    public function indexSetting(Request $request)
    {

        return view('TeamLead.settings.index' ,[
            'user' => $request->user(),
        ]);
    }

    // public function approveLead(Lead $lead)
    // {
    //     if ($lead->team_lead_id !== Auth::id()) {
    //         abort(403, 'Unauthorized action.');
    //     }
    //     $lead->update(['status' => 'approved']);
    //     $lead->employee->notifications()->create([
    //         'lead_id' => $lead->id,
    //         'message' => "Your lead '{$lead->name}' has been approved.",
    //     ]);
    //     // Notify Operations
    //     User::where('designation', 'operations')->get()->each(function ($user) use ($lead) {
    //         $user->notifications()->create([
    //             'lead_id' => $lead->id,
    //             'message' => "Lead '{$lead->name}' is ready for processing.",
    //         ]);
    //     });
    //     return redirect()->route('team_lead.leads.index')->with('success', 'Lead approved.');
    // }

    // public function rejectLead(Lead $lead)
    // {
    //     if ($lead->team_lead_id !== Auth::id()) {
    //         abort(403, 'Unauthorized action.');
    //     }
    //     $lead->update(['status' => 'rejected']);
    //     $lead->employee->notifications()->create([
    //         'lead_id' => $lead->id,
    //         'message' => "Your lead '{$lead->name}' has been rejected.",
    //     ]);
    //     return redirect()->route('team_lead.leads.index')->with('success', 'Lead rejected.');
    // }

  public function indexTasks()
{
   $employees = User::where('designation', 'employee')->get();


    return view('TeamLead.task.index', compact('employees'));
}

    // public function createTask()
    // {
    //     $employees = Auth::user()->employees()->get();
    //     return view('team-lead.tasks.create', compact('employees'));
    // }

    // public function storeTask(Request $request)
    // {
    //     $validated = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'employee_id' => 'nullable|exists:users,id',
    //     ]);

    //     $task = Auth::user()->assignedTasks()->create($validated + ['status' => 'pending']);

    //     if ($task->employee_id) {
    //         $task->employee->notifications()->create([
    //             'task_id' => $task->id,
    //             'message' => "New task '{$task->title}' assigned to you.",
    //         ]);
    //     }

    //     return redirect()->route('team_lead.tasks.index')->with('success', 'Task created successfully.');
    // }

    // public function bulkAssignTasks(Request $request)
    // {
    //     $validated = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'employee_ids' => 'required|array',
    //         'employee_ids.*' => 'exists:users,id',
    //     ]);

    //     foreach ($validated['employee_ids'] as $employeeId) {
    //         $task = Auth::user()->assignedTasks()->create([
    //             'title' => $validated['title'],
    //             'description' => $validated['description'],
    //             'employee_id' => $employeeId,
    //             'status' => 'pending',
    //         ]);
    //         User::find($employeeId)->notifications()->create([
    //             'task_id' => $task->id,
    //             'message' => "New task '{$task->title}' assigned to you.",
    //         ]);
    //     }

    //     return redirect()->route('team_lead.tasks.index')->with('success', 'Tasks assigned successfully.');
    // }

    // public function indexNotifications()
    // {
    //     $notifications = Auth::user()->notifications()->latest()->paginate(10);
    //     return view('team-lead.notifications.index', compact('notifications'));
    // }


public function filterLeadReport(Request $request)
{
    $query = Lead::query()->with(['employee', 'teamLead']);

    // 🧑‍💼 Only leads under this team lead
    $teamLeadId = auth()->id();
    $employeeIds = User::where('designation', 'employee')
        ->where('team_lead_id', $teamLeadId)
        ->pluck('id');
    $query->whereIn('employee_id', $employeeIds);

    // 🗓️ Date Range Filter
    if ($request->filled('date_range') && is_numeric($request->date_range)) {
        $query->whereBetween('created_at', [
            now()->subDays((int) $request->date_range)->startOfDay(),
            now()->endOfDay()
        ]);
    }

    // 📅 Custom Range
    if ($request->date_range === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('created_at', [
            Carbon::parse($request->start_date)->startOfDay(),
            Carbon::parse($request->end_date)->endOfDay(),
        ]);
    }

    // 🔍 Additional Filters
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('company')) {
        $query->where('company_name', $request->company);
    }

    if ($request->filled('state')) {
        $query->where('state', $request->state);
    }

    if ($request->filled('district')) {
        $query->where('district', $request->district);
    }

    if ($request->filled('city')) {
        $query->where('city', $request->city);
    }

    if ($request->filled('bank')) {
        $query->where('bank_name', $request->bank);
    }

    if ($request->filled('employee_id')) {
        $query->where('employee_id', $request->employee_id);
    }

    if ($request->filled('team_lead_id')) {
        $query->where('team_lead_id', $request->team_lead_id);
    }
    // 💰 Lead Amount Range
if ($request->filled('min_amount')) {
    $query->where('lead_amount', '>=', $request->min_amount);
}
if ($request->filled('max_amount')) {
    $query->where('lead_amount', '<=', $request->max_amount);
}


    // 💾 Fetch leads (paginated for leads table)
    $leads = $query->latest()->paginate(10)->appends($request->all());

    // 📊 Leads per employee (chart)
    $leadsPerEmployee = $query->get()->groupBy('employee_id')->map(function ($group, $empId) {
        return [
            'employee' => $group->first()->employee?->name ?? 'Unknown',
            'count' => $group->count()
        ];
    })->values();

    // 📊 Status Distribution (chart)
    $statusCounts = $query->get()->groupBy('status')->map(fn($group) => $group->count());

    // 👥 Team Performance (cards)
    $teamPerformance = User::where('designation', 'employee')
        ->where('team_lead_id', $teamLeadId)
        ->get()
        ->map(function ($employee) use ($query) {
            $employeeLeads = (clone $query)->where('employee_id', $employee->id)->get();
            $total = $employeeLeads->count();
            $avgSuccess = $employeeLeads->avg('success_percentage');
            return [
                'name' => $employee->name,
                'total_leads' => $total,
                'conversion_rate' => round($avgSuccess ?? 0, 1),
                'target_percentage' => min(100, round(($total / 50) * 100))
            ];
        });

    // 📦 Dashboard Stats
    $stats = [
        'total_leads' => $query->count(),
        'total_lead_value' => $query->sum('lead_amount'),
        'authorized_leads' => (clone $query)->where('status', 'authorized')->count(),
        'authorized_lead_value' => (clone $query)->where('status', 'authorized')->sum('lead_amount'),
        'login_leads' => (clone $query)->where('status', 'login')->count(),
        'login_lead_value' => (clone $query)->where('status', 'login')->sum('lead_amount'),
        'approved_leads' => (clone $query)->where('status', 'approved')->count(),
        'approved_lead_value' => (clone $query)->where('status', 'approved')->sum('lead_amount'),
        'disbursed_leads' => (clone $query)->where('status', 'disbursed')->count(),
        'disbursed_lead_value' => (clone $query)->where('status', 'disbursed')->sum('lead_amount'),
        'rejected_leads' => (clone $query)->where('status', 'rejected')->count(),
        'rejected_lead_value' => (clone $query)->where('status', 'rejected')->sum('lead_amount'),
        'active_employees' => User::where('designation', 'employee')
            ->where('team_lead_id', $teamLeadId)
            ->whereNull('deleted_at')
            ->count(),
    ];

    // 🔁 Dropdown data
    $teamLeads = User::where('designation', 'team_lead')->get(['id', 'name']);
    $employees = User::where('designation', 'employee')->where('team_lead_id', $teamLeadId)->get(['id', 'name']);
    $statuses = Lead::select('status')->distinct()->pluck('status')->filter()->values();
    $companies = Lead::select('company_name')->distinct()->pluck('company_name')->filter()->values();
    $states = Lead::select('state')->distinct()->pluck('state')->filter()->values();
    $districts = Lead::select('district')->distinct()->pluck('district')->filter()->values();
    $cities = Lead::select('city')->distinct()->pluck('city')->filter()->values();
    $banks = Lead::select('bank_name')->distinct()->pluck('bank_name')->filter()->values();

    return view('TeamLead.dashboard', compact(
        'stats',
        'teamLeads',
        'employees',
        'statuses',
        'companies',
        'states',
        'districts',
        'cities',
        'banks',
        'leads',
        'leadsPerEmployee',
        'statusCounts',
        'teamPerformance'
    ));
}

public function indexNotifications()
    {
        return view('TeamLead.Notification.notification');
    }

    public function fetch()
{
    $notifications = Notification::where('user_id', auth()->id())
        ->where('created_at', '>=', Carbon::now()->subDays(7)) // last 7 days
        ->orderBy('created_at', 'desc')
        ->get(['id', 'user_id', 'task_id', 'salary_slip_id', 'message', 'is_read', 'created_at']);

    return response()->json($notifications);
}


     public function markRead(Request $request, $id)
{
    Log::info('markRead called', [
        'id' => $id,
        'user_id' => auth()->id(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
    ]);

    try {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
            Log::info('Notification marked as read', [
                'id' => $id,
                'user_id' => auth()->id()
            ]);
        }

        return response()->json(['success' => true]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::warning('Notification not found or unauthorized', [
            'id' => $id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Notification not found or you are not authorized',
        ], 404);
    } catch (\Exception $e) {
        Log::error('Error marking notification as read', [
            'id' => $id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while marking the notification as read',
        ], 500);
    }
}

    // Optional: Uncomment if notification badge is needed

    public function countUnread()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
             ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
        return response()->json(['count' => $count]);
    }

public function download($id)
    {
        $salarySlip = SalarySlip::findOrFail($id);
        $filePath = storage_path('app/public/' . $salarySlip->pdf_path);
        return response()->download($filePath);
    }
}
