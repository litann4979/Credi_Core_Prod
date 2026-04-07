<?php

namespace App\Http\Controllers\OpearationController;

use App\Helpers\FormatHelper;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\SalarySlip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OperationDashboardController extends Controller
{
public function dashboardStats(Request $request)
    {
        $search = $request->input('search');
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $userId = auth()->id();

        $currentMonthName = Carbon::now()->format('F'); // Example: "August"
$currentYear = Carbon::now()->year;


        // Get team leads created by the operations user
        $teamLeadIds = User::where('designation', 'team_lead')
            ->where('created_by', $userId)
            ->pluck('id');

        // Get employees under those team leads
        $employeeIds = User::where('designation', 'employee')
            ->whereIn('team_lead_id', $teamLeadIds)
            ->pluck('id');


         $statuses = Lead::select('status')->distinct()->pluck('status')->toArray();
        // Query for leads forwarded to the operations user in the current month (for leads table)
        $leadQuery = Lead::with(['employee', 'teamLead'])
            ->join('lead_forwarded_histories', 'leads.id', '=', 'lead_forwarded_histories.lead_id')
            ->where('lead_forwarded_histories.receiver_user_id', $userId)
            ->where('lead_forwarded_histories.is_forwarded', 1)
            // ->whereYear('lead_forwarded_histories.forwarded_at', $currentYear)      // Year from forwarded_at
            ->where('leads.expected_month', $currentMonthName)
            ->where('leads.lead_type','!=','creditcard_loan');
        // Apply search filter
        if ($search) {
            $leadQuery->where(function ($q) use ($search) {
                $q->where('leads.name', 'like', "%{$search}%")
                  ->orWhere('leads.company_name', 'like', "%{$search}%")
                  ->orWhere('leads.state', 'like', "%{$search}%")
                  ->orWhere('leads.district', 'like', "%{$search}%")
                  ->orWhere('leads.city', 'like', "%{$search}%");
            });
        }

        // Fetch leads
        $leads = $leadQuery->select('leads.*')
            ->latest('lead_forwarded_histories.forwarded_at')
            ->get();

        // Stats query for leads forwarded to the operations user in the current month
        $statsQuery = Lead::join('lead_forwarded_histories', 'leads.id', '=', 'lead_forwarded_histories.lead_id')
            ->where('lead_forwarded_histories.receiver_user_id', $userId)
            ->where('lead_forwarded_histories.is_forwarded', 1)
            ->whereIn('leads.employee_id', $employeeIds)
            // ->whereYear('lead_forwarded_histories.forwarded_at', $currentYear)      // Year from forwarded_at
            ->where('leads.expected_month', $currentMonthName)
           ->where('leads.lead_type', '!=', 'creditcard_loan');


        // Calculate stats
        $stats = [
            'total_leads' => (clone $statsQuery)->count(),
            'total_lead_value' => (clone $statsQuery)->sum('leads.lead_amount'),
            'authorized_leads' => (clone $statsQuery)->where('leads.status', 'authorized')->count(),
            'authorized_lead_value' => (clone $statsQuery)->where('leads.status', 'authorized')->sum('leads.lead_amount'),
            'personal_leads' => (clone $statsQuery)->where('leads.status', 'personal_lead')->count(),
            'personal_lead_value' => (clone $statsQuery)->where('leads.status', 'personal_lead')->sum('leads.lead_amount'),
            'login_leads' => (clone $statsQuery)->where('leads.status', 'login')->count(),
            'login_lead_value' => (clone $statsQuery)->where('leads.status', 'login')->sum('leads.lead_amount'),
            'approved_leads' => (clone $statsQuery)->where('leads.status', 'approved')->count(),
            'approved_lead_value' => (clone $statsQuery)->where('leads.status', 'approved')->sum('leads.lead_amount'),
            'disbursed_leads' => (clone $statsQuery)->where('leads.status', 'disbursed')->count(),
            'disbursed_lead_value' => (clone $statsQuery)->where('leads.status', 'disbursed')->sum('leads.lead_amount'),
            'rejected_leads' => (clone $statsQuery)->where('leads.status', 'rejected')->count(),
            'rejected_lead_value' => (clone $statsQuery)->where('leads.status', 'rejected')->sum('leads.lead_amount'),
            'active_employees' => User::where('designation', 'employee')
                ->whereIn('team_lead_id', $teamLeadIds)
                ->whereNull('deleted_at')
                ->count(),
        ];

        // Calculate pending leads (leads not in authorized, login, approved, rejected)
        $knownLeadCount = $stats['login_leads'] + $stats['approved_leads'] + $stats['rejected_leads'] + $stats['disbursed_leads'];
        $stats['pending_leads'] = $stats['total_leads'] - $knownLeadCount;
        $stats['pending_lead_value'] = (clone $statsQuery)
            ->whereNotIn('leads.status', ['login', 'approved', 'rejected','disbursed'])
            ->sum('leads.lead_amount');



   // Credit card leads
$creditCardFromDate = $request->input('credit_card_from_date');
$creditCardToDate = $request->input('credit_card_to_date');
$creditCardMonth = $request->input('credit_card_month', Carbon::now()->format('F')); // Default to current month
$creditCardStatus = $request->input('credit_card_status', ''); // New status filter
$userId = auth()->id(); // ✅ Get current logged-in user ID

$creditCardLeadsQuery = Lead::query()
    ->select('leads.id', 'leads.name', 'leads.phone', 'leads.email', 'leads.dob', 'leads.city', 'leads.district', 'leads.state', 'leads.company_name', 'leads.lead_amount', 'leads.salary', 'leads.status', 'leads.lead_type', 'leads.turnover_amount', 'leads.bank_name', 'leads.employee_id')
    ->join('lead_forwarded_histories', 'leads.id', '=', 'lead_forwarded_histories.lead_id')
    ->where('leads.lead_type', 'creditcard_loan')
    ->where('lead_forwarded_histories.receiver_user_id', $userId)
    ->where('lead_forwarded_histories.is_forwarded', 1); // ✅ Only forwarded leads

// ✅ Apply Status filter if selected
if ($creditCardStatus) {
    $creditCardLeadsQuery->where('leads.status', $creditCardStatus);
}

// ✅ Apply date filters
if ($creditCardFromDate && $creditCardToDate) {
    $creditCardLeadsQuery->whereBetween('leads.created_at', [$creditCardFromDate, $creditCardToDate]);
}

$creditCardLeads = $creditCardLeadsQuery
    ->orderBy('leads.updated_at', 'desc') // ✅ Latest leads first
    ->get();


        return view('Opearation.dashboard', compact('stats', 'search', 'leads', 'creditCardLeads', 'creditCardFromDate', 'creditCardToDate', 'creditCardMonth','creditCardStatus','statuses',));
    }

    public function show(Request $request)
    {
        // Optionally pass leadId to the view
        $leadId = $request->query('leadId');
        return view('Opearation.creditcardlead-details', compact('leadId'));
    }



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

public function leadsByStatus(Request $request, $status)
{
    $currentMonthStart = Carbon::now()->startOfMonth();
    $currentMonthEnd = Carbon::now()->endOfMonth();
    $userId = auth()->id();
    $search = $request->input('search');


    $executiveFilter = $request->input('executive'); // Add this line
    $clientNameFilter = $request->input('client_name');
    $loanAccountFilter = $request->input('loan_account');
    $mobileFilter = $request->input('mobile');
    $companyFilter = $request->input('company');
    $loanAmountFilter = $request->input('loan_amount');
    $statusFilter = $request->input('status_filter'); // Different from route status
    $leadTypeFilter = $request->input('lead_type');
    $bankFilter = $request->input('bank');



     $currentMonthName = Carbon::now()->format('F'); // Example: "August"
$currentYear = Carbon::now()->year;

    // Get team leads created by the operations user
    $teamLeadIds = User::where('designation', 'team_lead')
        ->where('created_by', $userId)
        ->pluck('id');

    // Get employees under those team leads
    $employeeIds = User::where('designation', 'employee')
        ->whereIn('team_lead_id', $teamLeadIds)
        ->pluck('id');

    // Base query for leads
    $leadQuery = Lead::with(['employee', 'teamLead'])
        ->join('lead_forwarded_histories', 'leads.id', '=', 'lead_forwarded_histories.lead_id')
        ->where('lead_forwarded_histories.receiver_user_id', $userId)
        ->where('lead_forwarded_histories.is_forwarded', 1)
        ->whereIn('leads.employee_id', $employeeIds)
        //  ->whereYear('lead_forwarded_histories.forwarded_at', $currentYear)    // Year from forwarded_at
         ->where('leads.expected_month', $currentMonthName);
  


    // Apply executive filter if selected
    if ($executiveFilter) {
        $leadQuery->where('leads.employee_id', $executiveFilter);
    }
    // Apply client name filter
    if ($clientNameFilter) {
        $leadQuery->where('leads.name', 'like', "%{$clientNameFilter}%");
    }

    // Apply loan account filter
    if ($loanAccountFilter) {
        $leadQuery->where('leads.loan_account_number', 'like', "%{$loanAccountFilter}%");
    }

    // Apply mobile filter
    if ($mobileFilter) {
        $leadQuery->where('leads.phone', 'like', "%{$mobileFilter}%");
    }

    // Apply company filter
    if ($companyFilter) {
        $leadQuery->where('leads.company_name', 'like', "%{$companyFilter}%");
    }

    // Apply loan amount filter (you might want to implement range filtering)
    if ($loanAmountFilter) {
    if ($loanAmountFilter === '1-1000') {
        $leadQuery->whereBetween('leads.lead_amount', [1, 1000]);
    } elseif ($loanAmountFilter === '1000-10000') {
        $leadQuery->whereBetween('leads.lead_amount', [1000, 10000]);
    }elseif ($loanAmountFilter === '10000-100000') {
        $leadQuery->whereBetween('leads.lead_amount', [10000, 100000]);
    } elseif ($loanAmountFilter === '100000-1000000') {
        $leadQuery->whereBetween('leads.lead_amount', [100000, 1000000]);
    } elseif ($loanAmountFilter === '1000000+') {
        $leadQuery->where('leads.lead_amount', '>', 1000000);
    }
}


    // Apply status filter from dropdown (different from route status)
    if ($statusFilter) {
        $leadQuery->where('leads.status', $statusFilter);
    } else if ($status === 'total') {
        // No status filter for total leads
    } elseif ($status === 'pending') {
        $leadQuery->whereNotIn('leads.status', [ 'login', 'approved', 'rejected','disbursed']);
    } else {
        $leadQuery->where('leads.status', $status);
    }

    // Apply search filter
    if ($search) {
        $leadQuery->where(function ($q) use ($search) {
            $q->where('leads.name', 'like', "%{$search}%")
              ->orWhere('leads.company_name', 'like', "%{$search}%")
              ->orWhere('leads.state', 'like', "%{$search}%")
              ->orWhere('leads.district', 'like', "%{$search}%")
              ->orWhere('leads.city', 'like', "%{$search}%");
        });
    }

     // Apply lead type filter
    if ($leadTypeFilter) {
        $leadQuery->where('leads.lead_type', $leadTypeFilter);
    }

    // Apply bank filter
    if ($bankFilter) {
        $leadQuery->where('leads.bank_name', 'like', "%{$bankFilter}%");
    }

    $leads = $leadQuery->select('leads.*')
        ->latest('leads.updated_at')
        ->get();

    // Get executives for dropdown - employees under the operation user's team leads
    $executives = User::where('designation', 'employee')
        ->whereIn('team_lead_id', $teamLeadIds)
        ->whereHas('leads')
        ->orderBy('name')
        ->get(['id', 'name']);


        // Get unique values for other dropdowns
    $clientNames = Lead::whereIn('employee_id', $employeeIds)
        ->distinct()
        ->orderBy('name')
        ->pluck('name');

    $loanAccounts = Lead::whereIn('employee_id', $employeeIds)
        ->whereNotNull('loan_account_number')
        ->distinct()
        ->orderBy('loan_account_number')
        ->pluck('loan_account_number');

    $mobiles = Lead::whereIn('employee_id', $employeeIds)
        ->distinct()
        ->orderBy('phone')
        ->pluck('phone');

    $companies = Lead::whereIn('employee_id', $employeeIds)
        ->whereNotNull('company_name')
        ->distinct()
        ->orderBy('company_name')
        ->pluck('company_name');

    $statuses = Lead::whereIn('employee_id', $employeeIds)
        ->distinct()
        ->orderBy('status')
        ->pluck('status');

    $leadTypes = Lead::whereIn('employee_id', $employeeIds)
        ->whereNotNull('lead_type')
        ->distinct()
        ->orderBy('lead_type')
        ->pluck('lead_type');

    $banks = Lead::whereIn('employee_id', $employeeIds)
        ->whereNotNull('bank_name')
        ->distinct()
        ->orderBy('bank_name')
        ->pluck('bank_name');

          $banksName = \App\Models\Bank::where('is_active', true)
                ->pluck('bank_name')
                ->toArray();


        $formattedLeadTypes = $leadTypes
    ->filter(function ($type) {
        return $type !== 'creditcard_loan';
    })
    ->map(function ($type) {
        return [
            'value'   => $type,
            'display' => $this->getShortLeadType($type),
        ];
    })
    ->values() // reset keys (optional)
    ->toArray();



    // Prepare title for the view
    $title = ucfirst(str_replace('_', ' ', $status)) . ' Leads';
    if ($status === 'total') {
        $title = 'All Leads';
    }

    return view('Opearation.lead_by_status', compact(
        'leads',
        'search',
        'title',
        'status',
        'executives',
        'executiveFilter',
        'clientNames',
        'clientNameFilter',
        'loanAccounts',
        'loanAccountFilter',
        'mobiles',
        'mobileFilter',
        'companies',
        'companyFilter',
        'loanAmountFilter',
        'statuses',
        'statusFilter',
        'leadTypes',
        'leadTypeFilter',
        'banks',
        'banksName',
        'bankFilter',
        'formattedLeadTypes'
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
public function filterLeadReport(Request $request)
{
    Log::info('📥 Filter Request Received', $request->all());

    // 1️⃣ Get all team leads created by this operation user
    $teamLeadIds = User::where('designation', 'team_lead')
        ->where('created_by', auth()->id())
        ->pluck('id');

    // 2️⃣ Get all employees under those team leads
    $employeeQuery = User::where('designation', 'employee')
        ->whereIn('team_lead_id', $teamLeadIds);

    if ($request->filled('team_lead_id')) {
        $employeeQuery->where('team_lead_id', $request->team_lead_id);
    }

    $employeeIds = $employeeQuery->pluck('id');
    Log::info('👥 Matched Employee IDs', $employeeIds->toArray());

    // 3️⃣ Base Lead Query for those employees
    $query = Lead::with(['employee', 'teamLead'])->whereIn('employee_id', $employeeIds);

    // 4️⃣ Date Range
    if ($request->filled('date_range') && is_numeric($request->date_range)) {
        $days = (int) $request->date_range;
        $startDate = now('Asia/Kolkata')->subDays($days)->startOfDay();
        $endDate = now('Asia/Kolkata')->endOfDay();

        Log::info("📅 Applying date range filter", [
            'range_type' => 'last_days',
            'days' => $days,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $query->whereBetween('created_at', [$startDate, $endDate]);

    } elseif ($request->date_range === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            Log::info("📅 Applying custom date range", [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $query->whereBetween('created_at', [$startDate, $endDate]);
        } catch (\Exception $e) {
            Log::error('❌ Invalid date format: ' . $e->getMessage());
            return back()->withErrors(['date_range' => 'Invalid date format provided.']);
        }
    }

    // 5️⃣ Apply Additional Filters
    if ($request->filled('employee_id') && $employeeIds->contains($request->employee_id)) {
        Log::info("👤 Filtering by specific employee", ['employee_id' => $request->employee_id]);
        $query->where('employee_id', $request->employee_id);
    } else {
        Log::info("👥 No specific employee selected — using all valid employees");
        $query->whereIn('employee_id', $employeeIds);
    }

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
    if ($request->filled('min_amount')) {
        $query->where('lead_amount', '>=', $request->min_amount);
    }
    if ($request->filled('max_amount')) {
        $query->where('lead_amount', '<=', $request->max_amount);
    }

    // 6️⃣ Clone query for summary use
    $filteredQuery = clone $query;

    // 7️⃣ Paginate
    $leads = $filteredQuery->latest()->paginate(10)->appends($request->all());

    Log::info('📊 Total leads after filter: ' . $filteredQuery->count());

    Log::info('✅ Filtered Leads Result (Paginated)', [
        'total' => $leads->total(),
        'per_page' => $leads->perPage(),
        'current_page' => $leads->currentPage(),
        'lead_ids' => $leads->pluck('id')->toArray(),
        'lead_names' => $leads->pluck('name')->toArray(),
    ]);

    // 8️⃣ Leads per Employee
  $leadsPerEmployee = User::whereIn('id', $employeeIds)->get()->map(function ($emp) use ($filteredQuery) {
    $count = (clone $filteredQuery)->where('employee_id', $emp->id)->count();
    return ['employee' => $emp->name, 'count' => $count];
});


    Log::info('👤 Leads per employee:', $leadsPerEmployee->toArray());

    // 9️⃣ Status Distribution
    $statusCounts = $filteredQuery->get()->groupBy('status')->map(fn($group) => $group->count());

    Log::info('📌 Status counts:', $statusCounts->toArray());

    // 🔟 Team Performance
    $teamPerformance = User::whereIn('id', $employeeIds)
        ->get()
        ->map(function ($employee) use ($filteredQuery) {
            $employeeLeads = (clone $filteredQuery)->where('employee_id', $employee->id)->get();
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
        'total_leads' => $filteredQuery->count(),
        'total_lead_value' => $filteredQuery->sum('lead_amount'),
        'authorized_leads' => (clone $filteredQuery)->where('status', 'authorized')->count(),
        'authorized_lead_value' => (clone $filteredQuery)->where('status', 'authorized')->sum('lead_amount'),
        // 'personal_leads' => (clone $filteredQuery)->where('status', 'personal_lead')->count(),
        // 'personal_lead_value' => (clone $filteredQuery)->where('status', 'personal_lead')->sum('lead_amount'),
        'login_leads' => (clone $filteredQuery)->where('status', 'login')->count(),
        'login_lead_value' => (clone $filteredQuery)->where('status', 'login')->sum('lead_amount'),
        'approved_leads' => (clone $filteredQuery)->where('status', 'approved')->count(),
        'approved_lead_value' => (clone $filteredQuery)->where('status', 'approved')->sum('lead_amount'),
        'disbursed_leads' => (clone $filteredQuery)->where('status', 'disbursed')->count(),
        'disbursed_lead_value' => (clone $filteredQuery)->where('status', 'disbursed')->sum('lead_amount'),
        'rejected_leads' => (clone $filteredQuery)->where('status', 'rejected')->count(),
        'rejected_lead_value' => (clone $filteredQuery)->where('status', 'rejected')->sum('lead_amount'),
        'active_employees' => $employeeQuery->whereNull('deleted_at')->count(),
    ];

    // 🔁 Dropdowns
    $teamLeads = User::where('designation', 'team_lead')->get(['id', 'name']);
    $employees = User::where('designation', 'employee')->get(['id', 'name']);
    $statuses = Lead::select('status')->distinct()->pluck('status')->filter()->values();
    $companies = Lead::select('company_name')->distinct()->pluck('company_name')->filter()->values();
    $states = Lead::select('state')->distinct()->pluck('state')->filter()->values();
    $districts = Lead::select('district')->distinct()->pluck('district')->filter()->values();
    $cities = Lead::select('city')->distinct()->pluck('city')->filter()->values();
    $banks = Lead::select('bank_name')->distinct()->pluck('bank_name')->filter()->values();

    return view('Opearation.dashboard', compact(
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

public function exportLeads(Request $request)
{
    // 🌐 Get team leads created by this operation
    $teamLeadIds = User::where('designation', 'team_lead')
        ->where('created_by', auth()->id())
        ->pluck('id');

    $employeeQuery = User::where('designation', 'employee')
        ->whereIn('team_lead_id', $teamLeadIds);

    if ($request->filled('team_lead_id')) {
        $employeeQuery->where('team_lead_id', $request->team_lead_id);
    }

    $employeeIds = $employeeQuery->pluck('id');

    $query = Lead::with(['employee', 'teamLead'])
        ->whereIn('employee_id', $employeeIds)
        ->whereNull('deleted_at');

    // 📅 Date Range
    if ($request->filled('date_range') && is_numeric($request->date_range)) {
        $start = now('Asia/Kolkata')->subDays((int) $request->date_range)->startOfDay();
        $end = now('Asia/Kolkata')->endOfDay();
        $query->whereBetween('created_at', [$start, $end]);
    } elseif ($request->date_range === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('created_at', [
            Carbon::parse($request->start_date)->startOfDay(),
            Carbon::parse($request->end_date)->endOfDay()
        ]);
    }

    // 🧪 Other Filters
    if ($request->filled('employee_id')) $query->where('employee_id', $request->employee_id);
    if ($request->filled('status')) $query->where('status', $request->status);
    if ($request->filled('company')) $query->where('company_name', $request->company);
    if ($request->filled('state')) $query->where('state', $request->state);
    if ($request->filled('district')) $query->where('district', $request->district);
    if ($request->filled('city')) $query->where('city', $request->city);
    if ($request->filled('bank')) $query->where('bank_name', $request->bank);
    if ($request->filled('min_amount')) $query->where('lead_amount', '>=', $request->min_amount);
    if ($request->filled('max_amount')) $query->where('lead_amount', '<=', $request->max_amount);

    $leads = $query->get();

    // 📤 CSV Export
    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=filtered_leads.csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = ['Client Name', 'Employee', 'Team Lead', 'Company', 'Location', 'Lead Amount', 'Success Rate', 'Expected Month', 'Status'];

    $callback = function () use ($leads, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($leads as $lead) {
            fputcsv($file, [
                $lead->name ?? 'N/A',
                $lead->employee->name ?? 'N/A',
                $lead->teamLead->name ?? 'N/A',
                $lead->company_name ?? 'N/A',
                $lead->city ?? $lead->district ?? $lead->state ?? 'N/A',
                is_numeric($lead->lead_amount) ? '₹' . number_format($lead->lead_amount, 2) : 'N/A',
                $lead->success_percentage !== null ? $lead->success_percentage . '%' : '-',
                $lead->expected_month ?? 'N/A',
                strtoupper($lead->status ?? 'N/A'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}



    // public function indexLeads()
    // {
    //     $leads = Lead::where('status', 'approved')->paginate(10);
    //     return view('operations.leads.index', compact('leads'));
    // }

    // public function completeLead(Lead $lead)
    // {
    //     if ($lead->status !== 'approved') {
    //         abort(403, 'Only approved leads can be completed.');
    //     }
    //     $lead->update(['status' => 'completed']);
    //     $lead->employee->notifications()->create([
    //         'lead_id' => $lead->id,
    //         'message' => "Your lead '{$lead->name}' has been completed.",
    //     ]);
    //     if ($lead->teamLead) {
    //         $lead->teamLead->notifications()->create([
    //             'lead_id' => $lead->id,
    //             'message' => "Lead '{$lead->name}' has been completed.",
    //         ]);
    //     }
    //     return redirect()->route('operations.leads.index')->with('success', 'Lead completed.');
    // }

    // public function indexNotifications()
    // {
    //     $notifications = Auth::user()->notifications()->latest()->paginate(10);
    //     return view('operations.notifications.index', compact('notifications'));
    // }


    public function indexNotifications()
    {
        return view('Opearation.Notification.notification');
    }

    public function fetch()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->where('created_at', '>=', Carbon::now()->subDays(7)) // last 7 days
            ->orderBy('created_at', 'desc')
            ->get(['id', 'user_id', 'task_id','salary_slip_id', 'message', 'is_read', 'created_at']);

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
