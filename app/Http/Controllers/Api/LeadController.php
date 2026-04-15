<?php

namespace App\Http\Controllers\Api;

use App\Events\LiveDashboardUpdated;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadForwardedHistory;
use App\Models\LeadHistory;
use App\Models\OfficeRule;
use App\Models\Score;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
/**
 * Get a list of all leads with enhanced filtering
 *
 * @param Request $request
 * @return JsonResponse
 */
// public function index(Request $request): JsonResponse
//     {
//         $user = Auth::user();

//         // Get filter parameters
//         $leadType = $request->query('lead_type', 'all');
//         $status = $request->query('status', 'all');
//         $includeDeleted = filter_var($request->query('include_deleted', false), FILTER_VALIDATE_BOOLEAN);
//         $search = $request->query('search');
//         $expectedMonth = $request->query('expected_month', Carbon::now()->format('F'));
// // dd($expectedMonth);
//         // Validate filter parameters
//         $validLeadTypes = ['all', 'personal_loan', 'home_loan', 'business_loan', 'creditcard_loan', 'future_lead'];
//         $validStatuses = ['all', 'personal_lead', 'pending', 'authorized', 'login', 'approved', 'disbursed', 'rejected', 'future_lead'];
//         $validExpectedMonths = ['all', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

//         if (!in_array($leadType, $validLeadTypes)) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Invalid lead type',
//             ], 400);
//         }

//         if (!in_array($status, $validStatuses)) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Invalid status',
//             ], 400);
//         }

//         if (!in_array($expectedMonth, $validExpectedMonths)) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Invalid expected month',
//             ], 400);
//         }

//         // Base query for leads retrieval
//         $query = Lead::query();

//         // Exclude creditcard_loan and future_lead when lead_type=all and status=all
//         if ($leadType === 'all' && $status === 'all') {
//             $query->whereNotIn('lead_type', ['creditcard_loan'])
//                   ->where('status', '!=', 'future_lead');
//         } else {
//             // Only exclude creditcard_loan if not explicitly requested
//             if ($leadType !== 'creditcard_loan' && $leadType !== 'all') {
//                 $query->whereNotIn('lead_type', ['creditcard_loan']);
//             }
//             // Only exclude future_lead if not explicitly requested
//             if ($status !== 'future_lead' && $status !== 'all') {
//                 $query->where('status', '!=', 'future_lead');
//             }
//         }

//         if ($includeDeleted && $user->designation === 'admin') {
//             $query->withTrashed();
//         } elseif ($includeDeleted) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Only admins can view deleted leads',
//             ], 403);
//         }

//         // Role-based filtering
//         if ($user->designation !== 'team_lead' && $user->designation !== 'operations' && $user->designation !== 'admin') {
//             $query->where('employee_id', $user->id);
//         } elseif ($user->designation === 'team_lead') {
//             $query->where(function ($q) use ($user) {
//                 $q->where('employee_id', $user->id)
//                   ->orWhere('team_lead_id', $user->id);
//             });
//         }

//         // Apply search filter
//         if ($search) {
//             $query->where(function ($q) use ($search) {
//                 $q->where('name', 'like', "%{$search}%")
//                   ->orWhere('phone', 'like', "%{$search}%")
//                   ->orWhere('email', 'like', "%{$search}%")
//                   ->orWhere('bank_name', 'like', "%{$search}%")
//                   ->orWhere('state', 'like', "%{$search}%")
//                   ->orWhere('district', 'like', "%{$search}%")
//                   ->orWhere('city', 'like', "%{$search}%")
//                   ->orWhere('location', 'like', "%{$search}%");
//             });
//         }

//         // Apply expected month filter
//         if ($expectedMonth !== 'all') {
//             $query->where('expected_month', $expectedMonth);
//         }

//         // Apply lead type filter
//         if ($leadType !== 'all') {
//             $query->where('lead_type', $leadType);
//         }

//         // Apply status filter
//         if ($status !== 'all') {
//             $query->where('status', $status);
//         }

//         // Get all leads with relationships
//         $leads = $query->with([
//             'employee',
//             'teamLead',
//             'histories' => function ($query) {
//                 $query->orderBy('created_at', 'desc')->with('user');
//             },
//             'forwardedHistories' => function ($query) {
//                 $query->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver']);
//             }
//         ])->orderBy('created_at', 'desc')->get();

//         // Transform leads to include combined location
//         $leads->transform(function ($lead) {
//             $lead->combined_location = trim("{$lead->state}, {$lead->district}, {$lead->city}, {$lead->location}", ', ');
//             $lead->lead_amount = number_format($lead->lead_amount, 2, '.', '');
//             return $lead;
//         });

//         // Aggregate data
//         $aggregatesQuery = Lead::query();

//         // Apply the same filters as the main query
//         if ($leadType === 'all' && $status === 'all') {
//             $aggregatesQuery->whereNotIn('lead_type', ['creditcard_loan'])
//                             ->where('status', '!=', 'future_lead');
//         } else {
//             if ($leadType !== 'creditcard_loan' && $leadType !== 'all') {
//                 $aggregatesQuery->whereNotIn('lead_type', ['creditcard_loan']);
//             }
//             if ($status !== 'future_lead' && $status !== 'all') {
//                 $aggregatesQuery->where('status', '!=', 'future_lead');
//             }
//         }

//         if ($includeDeleted && $user->designation === 'admin') {
//             $aggregatesQuery->withTrashed();
//         }

//         if ($user->designation !== 'team_lead' && $user->designation !== 'operations' && $user->designation !== 'admin') {
//             $aggregatesQuery->where('employee_id', $user->id);
//         } elseif ($user->designation === 'team_lead') {
//             $aggregatesQuery->where(function ($q) use ($user) {
//                 $q->where('employee_id', $user->id)
//                   ->orWhere('team_lead_id', $user->id);
//             });
//         }

//         if ($search) {
//             $aggregatesQuery->where(function ($q) use ($search) {
//                 $q->where('name', 'like', "%{$search}%")
//                   ->orWhere('phone', 'like', "%{$search}%")
//                   ->orWhere('email', 'like', "%{$search}%")
//                   ->orWhere('bank_name', 'like', "%{$search}%")
//                   ->orWhere('state', 'like', "%{$search}%")
//                   ->orWhere('district', 'like', "%{$search}%")
//                   ->orWhere('city', 'like', "%{$search}%")
//                   ->orWhere('location', 'like', "%{$search}%");
//             });
//         }

//         if ($expectedMonth !== 'all') {
//             $aggregatesQuery->where('expected_month', $expectedMonth);
//         }

//         if ($leadType !== 'all') {
//             $aggregatesQuery->where('lead_type', $leadType);
//         }

//         if ($status !== 'all') {
//             $aggregatesQuery->where('status', $status);
//         }

//         $aggregates = [
//             'total_leads' => [
//                 'count' => $aggregatesQuery->count(),
//                 'total_amount' => $aggregatesQuery->sum('lead_amount') ?? 0,
//             ],
//             'status_breakdown' => []
//         ];

//         // Define statuses to include in aggregates
//         $aggregateStatuses = array_diff($validStatuses, ['all', 'future_lead']);
//         if ($status === 'future_lead' || ($status === 'all' && $leadType !== 'all')) {
//             $aggregateStatuses[] = 'future_lead';
//         }

//         foreach ($aggregateStatuses as $validStatus) {
//             $aggregates['status_breakdown'][$validStatus] = [
//                 'count' => $aggregatesQuery->clone()->where('status', $validStatus)->count(),
//                 'total_amount' => $aggregatesQuery->clone()->where('status', $validStatus)->sum('lead_amount') ?? 0,
//             ];
//         }

//         return response()->json([
//             'status' => 'success',
//             'message' => 'Leads retrieved successfully',
//             'data' => [
//                 'leads' => $leads,
//                 'aggregates' => $aggregates,
//                 'filters_applied' => [
//                     'lead_type' => $leadType,
//                     'status' => $status,
//                     'include_deleted' => $includeDeleted,
//                     'search' => $search,
//                     'expected_month' => $expectedMonth,
//                 ],
//             ],
//         ], 200);
//     }

public function index(Request $request): JsonResponse
{
    $user = Auth::user();

    $leadType       = $request->query('lead_type', 'all');
    $status         = $request->query('status', 'all');
    $includeDeleted = filter_var($request->query('include_deleted', false), FILTER_VALIDATE_BOOLEAN);
    $search         = $request->query('search');
    $expectedMonth  = $request->query('expected_month', Carbon::now()->format('F'));
    $currentYear    = Carbon::now()->year;

    $validLeadTypes = ['all', 'personal_loan', 'home_loan', 'business_loan', 'creditcard_loan', 'future_lead'];
    $validStatuses  = ['all', 'personal_lead', 'pending', 'authorized', 'login', 'approved', 'disbursed', 'rejected', 'future_lead'];
    $validMonths    = ['all', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    if (!in_array($leadType, $validLeadTypes)) {
        return response()->json(['status' => 'error', 'message' => 'Invalid lead type'], 400);
    }

    if (!in_array($status, $validStatuses)) {
        return response()->json(['status' => 'error', 'message' => 'Invalid status'], 400);
    }

    if (!in_array($expectedMonth, $validMonths)) {
        return response()->json(['status' => 'error', 'message' => 'Invalid expected month'], 400);
    }

    /*
    |--------------------------------------------------------------------------
    | Shared Filter Builder (Production Safe)
    |--------------------------------------------------------------------------
    */

    $buildQuery = function () use (
        $user,
        $leadType,
        $status,
        $includeDeleted,
        $search,
        $expectedMonth,
        $currentYear
    ) {

        $query = Lead::query();

        // Soft delete handling
        if ($includeDeleted && $user->designation === 'admin') {
            $query->withTrashed();
        } elseif ($includeDeleted) {
            abort(403, 'Only admins can view deleted leads');
        }

        // Role-based filtering
        if (!in_array($user->designation, ['team_lead', 'operations', 'admin'])) {
            $query->where('employee_id', $user->id);
        } elseif ($user->designation === 'team_lead') {
            $query->where(function ($q) use ($user) {
                $q->where('employee_id', $user->id)
                  ->orWhere('team_lead_id', $user->id);
            });
        }

        // 🔥 CURRENT YEAR FILTER (FIXED ISSUE)
      if (
    !($status === 'future_lead' || $leadType === 'creditcard_loan')
) {
    $query->whereYear('created_at', $currentYear);
}

        // Default exclusions
        if ($leadType === 'all' && $status === 'all') {
            $query->whereNotIn('lead_type', ['creditcard_loan'])
                  ->where('status', '!=', 'future_lead');
        } else {
            if ($leadType !== 'creditcard_loan' && $leadType !== 'all') {
                $query->whereNotIn('lead_type', ['creditcard_loan']);
            }

            if ($status !== 'future_lead' && $status !== 'all') {
                $query->where('status', '!=', 'future_lead');
            }
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Expected month
        if ($expectedMonth !== 'all') {
            $query->where('expected_month', $expectedMonth);
        }

        // Lead type filter
        if ($leadType !== 'all') {
            $query->where('lead_type', $leadType);
        }

        // Status filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return $query;
    };

    /*
    |--------------------------------------------------------------------------
    | Main Leads Query
    |--------------------------------------------------------------------------
    */

    $leadsQuery = $buildQuery();

    $leads = $leadsQuery->with([
        'employee',
        'teamLead',
        'histories' => fn($q) => $q->orderBy('created_at', 'desc')->with('user'),
        'forwardedHistories' => fn($q) => $q->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver'])
    ])
    ->orderBy('created_at', 'desc')
    ->get()
    ->transform(function ($lead) {
        $lead->combined_location = trim("{$lead->state}, {$lead->district}, {$lead->city}, {$lead->location}", ', ');
        $lead->lead_amount = number_format($lead->lead_amount, 2, '.', '');
        return $lead;
    });

    /*
    |--------------------------------------------------------------------------
    | Aggregates (Using Same Filters = No Mismatch Ever Again)
    |--------------------------------------------------------------------------
    */

    $aggregatesBase = $buildQuery();

    $aggregates = [
        'total_leads' => [
            'count' => $aggregatesBase->count(),
            'total_amount' => $aggregatesBase->sum('lead_amount') ?? 0,
        ],
        'status_breakdown' => []
    ];

    $aggregateStatuses = array_diff($validStatuses, ['all']);

    foreach ($aggregateStatuses as $validStatus) {
        $aggregates['status_breakdown'][$validStatus] = [
            'count' => $buildQuery()->where('status', $validStatus)->count(),
            'total_amount' => $buildQuery()->where('status', $validStatus)->sum('lead_amount') ?? 0,
        ];
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Leads retrieved successfully',
        'data' => [
            'leads' => $leads,
            'aggregates' => $aggregates,
            'filters_applied' => [
                'lead_type' => $leadType,
                'status' => $status,
                'include_deleted' => $includeDeleted,
                'search' => $search,
                'expected_month' => $expectedMonth,
                'year' => $currentYear,
            ],
        ],
    ], 200);
}

    /**
     * Create a new lead with history tracking
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        if ($user->designation === 'operations' && $user->designation !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Operations team cannot create leads',
            ], 403);
        }

        $leadType = $request->input('lead_type');

        // Common validation rules
        $commonRules = [
            'lead_type' => 'required|string|in:personal_loan,home_loan,business_loan,creditcard_loan,future_lead',
            'team_lead_id' => 'nullable|exists:users,id',
            'voice_recording' => 'nullable|file|mimes:mp3,wav,aac,m4a,ogg,flac|max:10240',
            'forward_to' => 'nullable|exists:users,id',
        ];

        // Specific validation rules based on lead_type
        $specificRules = [];
        if ($leadType === 'personal_loan' || $leadType === 'home_loan') {
            $specificRules = [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
                'state' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'lead_amount' => 'required|numeric|min:0',
                'expected_month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                'email' => 'nullable|string|email|max:255',
                'dob' => 'nullable|date|before:today',
                'company_name' => 'nullable|string|max:255',
                'salary' => 'nullable|numeric|min:0',
                'success_percentage' => 'nullable|integer|min:0|max:100',
                'remarks' => 'nullable|string|max:1000',
            ];
        } elseif ($leadType === 'business_loan') {
            $specificRules = [
                'business_name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
                'email' => 'nullable|string|email|max:255',
                'state' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'lead_amount' => 'required|numeric|min:0',
                'turnover_amount' => 'required|numeric|min:5000000',
                'vintage_year' => 'required|integer|min:2',
                'it_return' => 'required|numeric|min:0',
                'success_percentage' => 'nullable|integer|min:0|max:100',
                'remarks' => 'nullable|string|max:1000',
            ];
        } elseif ($leadType === 'creditcard_loan') {
            $specificRules = [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
                'email' => 'required|string|email|max:255',
                'bank_names' => 'required|array|min:1|max:2',
                'bank_names.*' => 'required|string|max:255|distinct',
            ];
        } elseif ($leadType === 'future_lead') {
            $specificRules = [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
                'location' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255',
                'company_name' => 'nullable|string|max:255',
                'lead_amount' => 'nullable|numeric|min:0',
            ];
        }

        $validator = Validator::make($request->all(), array_merge($commonRules, $specificRules));

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate forward_to user designation
        if ($request->has('forward_to')) {
            $forwardToUser = User::find($request->forward_to);
            if (!$forwardToUser || !in_array($forwardToUser->designation, ['admin', 'team_lead', 'operations'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forwarded user must have designation admin, team_lead, or operations',
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            $voice_recording_path = null;
            if ($request->hasFile('voice_recording')) {
                $voice_recording_path = $request->file('voice_recording')->store('voice_recordings', 'public');
            }
            $final_voice_recording_path = $voice_recording_path ? '/storage/' . $voice_recording_path : null;

            // Prepare base data
            $baseData = [
                'employee_id' => Auth::id(),
                'team_lead_id' => $request->team_lead_id,
                'status' => $leadType === 'future_lead' ? 'future_lead' : 'personal_lead',
                'lead_type' => $leadType,
                'voice_recording' => $final_voice_recording_path,
                'is_personal_lead' => $leadType === 'future_lead' ? false : true,
            ];

            $leads = [];
            if ($leadType === 'creditcard_loan') {
                // Create a lead for each bank_name
                foreach ($request->bank_names as $bank) {
                    $data = array_merge($baseData, [
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'bank_name' => $bank,
                    ]);

                    $lead = Lead::create($data);
                    LeadHistory::create([
                        'lead_id' => $lead->id,
                        'user_id' => Auth::id(),
                        'action' => 'created',
                        'status' => 'personal_lead',
                        'forwarded_to' => $request->forward_to,
                        'comments' => "Created credit card lead for bank: {$bank}" .
                            ($request->forward_to ? " and forwarded to user ID {$request->forward_to}" : ''),
                    ]);

                    if ($request->forward_to) {
                        $lead->update(['team_lead_id' => $request->forward_to]);
                        LeadHistory::create([
                            'lead_id' => $lead->id,
                            'user_id' => Auth::id(),
                            'action' => 'forwarded',
                            'status' => 'personal_lead',
                            'forwarded_to' => $request->forward_to,
                            'comments' => "Forwarded to user ID {$request->forward_to}",
                        ]);
                        LeadForwardedHistory::create([
                            'lead_id' => $lead->id,
                            'sender_user_id' => Auth::id(),
                            'receiver_user_id' => $request->forward_to,
                            'is_forwarded' => true,
                            'forwarded_at' => now(),
                        ]);
                    }

                    $lead->load([
                        'employee',
                        'teamLead',
                        'histories' => function ($query) {
                            $query->orderBy('created_at', 'desc')->with('user');
                        },
                        'forwardedHistories' => function ($query) {
                            $query->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver']);
                        }
                    ]);
                    $leads[] = $lead;
                }
            } else {
                // Prepare data based on lead_type
                $data = $baseData;
                if ($leadType === 'personal_loan' || $leadType === 'home_loan') {
                    $data = array_merge($data, [
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'dob' => $request->dob,
                        'state' => $request->state,
                        'district' => $request->district,
                        'city' => $request->city,
                        'company_name' => $request->company_name,
                        'lead_amount' => $request->lead_amount,
                        'salary' => $request->salary,
                        'success_percentage' => $request->success_percentage,
                        'expected_month' => $request->expected_month,
                        'remarks' => $request->remarks,
                    ]);
                } elseif ($leadType === 'business_loan') {
                    $data = array_merge($data, [
                        'name' => $request->business_name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'state' => $request->state,
                        'district' => $request->district,
                        'city' => $request->city,
                        'lead_amount' => $request->lead_amount,
                        'turnover_amount' => $request->turnover_amount,
                        'vintage_year' => $request->vintage_year,
                        'it_return' => $request->it_return,
                        'success_percentage' => $request->success_percentage,
                        'remarks' => $request->remarks,
                    ]);
                } elseif ($leadType === 'future_lead') {
                    $data = array_merge($data, [
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'location' => $request->location,
                        'email' => $request->email,
                        'company_name' => $request->company_name,
                        'lead_amount' => $request->lead_amount,
                    ]);
                }

                $lead = Lead::create($data);

                // ✅ Lead Score Logic
$userId = Auth::id();
$today = Carbon::today();

// Count today's leads
$todayLeadCount = Lead::where('employee_id', $userId)
    ->whereDate('created_at', $today)
    ->count();

// Get or create score row
$score = Score::where('user_id', $userId)
    ->whereDate('date', $today)
    ->first();

if (!$score) {
    $score = new Score();
    $score->user_id = $userId;
    $score->date = $today;
    $score->target_score = 0;
    $score->additional_target_score = 0;
    $score->lead_score = 0;
    $score->additional_lead_score = 0;
    $score->attendance_score = 0;
    $score->leave_score = 0;
    $score->total_score = 0;
}

// ✅ Apply scoring
// Use office rule config with fallback defaults.
$officeRule = OfficeRule::query()->latest('id')->first();
$officeRule = OfficeRule::query()->latest('id')->first();

$personalLeadCount = (int) ($officeRule->personal_lead_count ?? 2);
$leadMark = (float) ($officeRule->lead_mark ?? 30);

if ($personalLeadCount <= 0) {
    $personalLeadCount = 2;
}
if ($leadMark <= 0) {
    $leadMark = 30;
}

// ✅ Per lead value
$perLeadMark = $leadMark / $personalLeadCount;

// ✅ Calculate
if ($todayLeadCount <= $personalLeadCount) {
    $score->lead_score = $todayLeadCount * $perLeadMark;
    $score->additional_lead_score = 0;
} else {
    $score->lead_score = $leadMark; // FIXED max
    $additionalLeadCount = $todayLeadCount - $personalLeadCount;
    $score->additional_lead_score = $additionalLeadCount * $perLeadMark;
}

// ✅ Recalculate total score
$score->total_score =
    $score->target_score +
    $score->lead_score +
    $score->discipline_score;
    
$score->save();
                LeadHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => Auth::id(),
                    'action' => 'created',
                    'status' => $leadType === 'future_lead' ? 'future_lead' : 'personal_lead',
                    'forwarded_to' => $request->forward_to,
                    'comments' => 'Lead created' .
                        ($request->forward_to ? " and forwarded to user ID {$request->forward_to}" : ''),
                ]);

                if ($request->forward_to) {
                    $lead->update(['team_lead_id' => $request->forward_to]);
                    LeadHistory::create([
                        'lead_id' => $lead->id,
                        'user_id' => Auth::id(),
                        'action' => 'forwarded',
                        'status' => $leadType === 'future_lead' ? 'future_lead' : 'personal_lead',
                        'forwarded_to' => $request->forward_to,
                        'comments' => "Forwarded to user ID {$request->forward_to}",
                    ]);
                    LeadForwardedHistory::create([
                        'lead_id' => $lead->id,
                        'sender_user_id' => Auth::id(),
                        'receiver_user_id' => $request->forward_to,
                        'is_forwarded' => true,
                        'forwarded_at' => now(),
                    ]);
                }

                $lead->load([
                    'employee',
                    'teamLead',
                    'histories' => function ($query) {
                        $query->orderBy('created_at', 'desc')->with('user');
                    },
                    'forwardedHistories' => function ($query) {
                        $query->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver']);
                    }
                ]);
                $leads[] = $lead;
            }

            DB::commit();
            event(new LiveDashboardUpdated((int) Auth::id()));

            return response()->json([
                'status' => 'success',
                'message' => count($leads) > 1 ? 'Leads created successfully' : 'Lead created successfully',
                'data' => $leads,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            if ($voice_recording_path) {
                Storage::disk('public')->delete($voice_recording_path);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific lead by ID with history
     *
     * @param Lead $lead
     * @return JsonResponse
     */
    public function show(Lead $lead): JsonResponse
    {
        $user = Auth::user();
        if ($lead->trashed() && $user->designation !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead not found',
            ], 404);
        }

        $lead->load([
            'employee',
            'teamLead',
            'histories' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            },
            'forwardedHistories' => function ($query) {
                $query->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver']);
            }
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Lead retrieved successfully',
            'data' => $lead,
        ], 200);
    }

    /**
     * Get lead data for editing
     *
     * @param Lead $lead
     * @return JsonResponse
     */
    public function edit(Lead $lead): JsonResponse
    {
        $user = Auth::user();
        if ($lead->trashed() && $user->designation !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead not found',
            ], 404);
        }

        // Check if lead is editable (personal_lead or future_lead) unless user is admin
        if ($user->designation !== 'admin' && !in_array($lead->status, ['personal_lead', 'future_lead'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead cannot be edited as it is not in personal_lead or future_lead status',
            ], 403);
        }

        // if (
        //     $user->designation !== 'team_lead' && $user->designation !== 'operations' &&
        //     $user->designation !== 'admin' && !($lead->is_personal_lead && Auth::id() === $lead->employee_id)
        // ) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Unauthorized to edit this lead',
        //     ], 403);
        // }

        $lead->load([
            'employee',
            'teamLead',
            'histories' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            },
            'forwardedHistories' => function ($query) {
                $query->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver']);
            }
        ]);

        $leadData = [
            'id' => $lead->id,
            'status' => $lead->status,
            'lead_type' => $lead->lead_type,
            'voice_recording' => $lead->voice_recording,
            'team_lead_id' => $lead->team_lead_id,
            'is_personal_lead' => $lead->is_personal_lead,
            'created_at' => $lead->created_at->toISOString(),
            'deleted_at' => $lead->deleted_at ? $lead->deleted_at->toISOString() : null,
            'employee' => [
                'name' => $lead->employee ? $lead->employee->email : null,
                'profile_photo_url' => null,
                'pan_card_url' => null,
                'aadhar_card_url' => null,
                'signature_url' => null,
            ],
            'histories' => $lead->histories,
            'forwardedHistories' => $lead->forwardedHistories,
        ];

        if ($lead->lead_type === 'personal_loan' || $lead->lead_type === 'home_loan') {
            $leadData = array_merge($leadData, [
                'name' => $lead->name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'dob' => $lead->dob,
                'state' => $lead->state,
                'district' => $lead->district,
                'city' => $lead->city,
                'company_name' => $lead->company_name,
                'lead_amount' => number_format($lead->lead_amount, 2, '.', ''),
                'salary' => $lead->salary ? number_format($lead->salary, 2, '.', '') : null,
                'success_percentage' => $lead->success_percentage,
                'expected_month' => $lead->expected_month,
                'remarks' => $lead->remarks,
            ]);
        } elseif ($lead->lead_type === 'business_loan') {
            $leadData = array_merge($leadData, [
                'business_name' => $lead->name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'state' => $lead->state,
                'district' => $lead->district,
                'city' => $lead->city,
                'lead_amount' => number_format($lead->lead_amount, 2, '.', ''),
                'turnover_amount' => number_format($lead->turnover_amount, 2, '.', ''),
                'vintage_year' => $lead->vintage_year,
                'it_return' => number_format($lead->it_return, 2, '.', ''),
                'success_percentage' => $lead->success_percentage,
                'remarks' => $lead->remarks,
            ]);
        } elseif ($lead->lead_type === 'creditcard_loan') {
            $leadData = array_merge($leadData, [
                'name' => $lead->name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'bank_name' => $lead->bank_name,
            ]);
        } elseif ($lead->lead_type === 'future_lead') {
            $leadData = array_merge($leadData, [
                'name' => $lead->name,
                'phone' => $lead->phone,
                'location' => $lead->location,
                'email' => $lead->email,
                'company_name' => $lead->company_name,
                'lead_amount' => number_format($lead->lead_amount, 2, '.', ''),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lead data retrieved for editing',
            'data' => [
                'lead' => $leadData,
            ],
        ], 200);
    }

    /**
     * Update an existing lead with history tracking
     *
     * @param Request $request
     * @param Lead $lead
     * @return JsonResponse
     */
    public function update(Request $request, Lead $lead): JsonResponse
    {
        $user = Auth::user();

        if ($lead->trashed() && $user->designation !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead not found',
            ], 404);
        }

        // Check if lead is editable (personal_lead or future_lead) unless user is admin
        if ($user->designation !== 'admin' && !in_array($lead->status, ['personal_lead', 'future_lead'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead cannot be updated as it is not in personal_lead or future_lead status',
            ], 403);
        }

        // Authorization check
        // if (
        //     $user->designation !== 'team_lead' && $user->designation !== 'operations' &&
        //     $user->designation !== 'admin' && !($lead->is_personal_lead && Auth::id() === $lead->employee_id)
        // ) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Unauthorized to update this lead',
        //     ], 403);
        // }

        // Define status transitions
        $statusTransitions = [
            'team_lead' => [
                'personal_lead' => ['pending', 'rejected', 'future_lead'],
                'future_lead' => ['pending', 'rejected'],
                'pending' => ['authorized', 'rejected'],
                'authorized' => ['login', 'rejected'],
            ],
            'operations' => [
                'login' => ['approved', 'rejected'],
                'approved' => ['disbursed', 'rejected'],
            ],
            'admin' => ['personal_lead', 'pending', 'authorized', 'login', 'approved', 'disbursed', 'rejected', 'future_lead'],
            'employee' => ['personal_lead', 'future_lead'],
        ];

        $validStatuses = [];
        if ($user->designation === 'admin') {
            $validStatuses = $statusTransitions['admin'];
        } elseif ($user->designation === 'team_lead') {
            $validStatuses = $statusTransitions['team_lead'][$lead->status] ?? [];
        } elseif ($user->designation === 'operations') {
            $validStatuses = $statusTransitions['operations'][$lead->status] ?? [];
        } elseif ($lead->is_personal_lead && Auth::id() === $lead->employee_id) {
            $validStatuses = $statusTransitions['employee'];
        }

        // Common validation rules
        $commonRules = [
            'status' => 'sometimes|string|in:' . implode(',', $validStatuses),
            'team_lead_id' => 'sometimes|nullable|exists:users,id',
            'voice_recording' => 'nullable|file|mimes:mp3,wav,aac,m4a,ogg,flac|max:20480',
            'forward_to' => 'nullable|exists:users,id',
            'forward_notes' => 'nullable|string|max:5000',
        ];

        // Specific validation rules based on lead_type
        $specificRules = [];
        if ($lead->lead_type === 'personal_loan' || $lead->lead_type === 'home_loan') {
            $specificRules = [
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|regex:/^\+?[1-9]\d{1,14}$/',
                'email' => 'sometimes|nullable|string|email|max:255',
                'dob' => 'nullable|date|before:today',
                'state' => 'sometimes|string|max:255',
                'district' => 'sometimes|string|max:255',
                'city' => 'sometimes|string|max:255',
                'company_name' => 'sometimes|nullable|string|max:255',
                'lead_amount' => 'sometimes|amount',
                'salary' => 'sometimes|nullable|numeric|min:0',
                'success_percentage' => 'sometimes|nullable|integer|min:0|max:100',
                'expected_month' => 'sometimes|nullable|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                'remarks' => 'sometimes|nullable|string|max:1000',
            ];
        } elseif ($lead->lead_type === 'business_loan') {
            $specificRules = [
                'business_name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|regex:/^\+?[1-9]\d{1,14}$/',
                'email' => 'sometimes|nullable|string|email|max:255',
                'state' => 'sometimes|string|max:255',
                'district' => 'sometimes|string|max:255',
                'city' => 'sometimes|string|max:255',
                'lead_amount' => 'sometimes|amount',
                'turnover_amount' => 'sometimes|nullable|numeric|min:5000000',
                'vintage_year' => 'sometimes|nullable|integer|min:2',
                'it_return' => 'sometimes|nullable|numeric|min:0',
                'success_percentage' => 'sometimes|nullable|integer|min:0|max:100',
                'remarks' => 'sometimes|nullable|string|max:1000',
            ];
        } elseif ($lead->lead_type === 'creditcard_loan') {
            $specificRules = [
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|regex:/^\+?[1-9]\d{1,14}$/',
                'email' => 'sometimes|string|email|max:255',
                'bank_name' => 'sometimes|string|max:255',
            ];
        } elseif ($lead->lead_type === 'future_lead') {
            $specificRules = [
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|regex:/^\+?[1-9]\d{1,14}$/',
                'location' => 'sometimes|string|max:255',
                'email' => 'sometimes|nullable|string|email|max:255',
                'company_name' => 'sometimes|nullable|string|max:255',
                'lead_amount' => 'sometimes|amount',
            ];
        }

        // Register custom amount validator
        Validator::extend('amount', function ($attribute, $value) {
            return is_numeric($value) && $value >= 0 && preg_match('/^\d+(\.\d{1,2})?$/', $value);
        }, 'The :attribute must be a valid amount with up to 2 decimal places.');

        $validator = Validator::make($request->all(), array_merge($commonRules, $specificRules));

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate forward_to user designation
        if ($request->has('forward_to')) {
            $forwardToUser = User::find($request->forward_to);
            if (!$forwardToUser || !in_array($forwardToUser->designation, ['admin', 'team_lead', 'operations'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forwarded user must have designation admin, team_lead, or operations',
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Initialize data with validated fields
            $data = $request->only(array_keys(array_merge($commonRules, $specificRules)));

            // Update is_personal_lead when team lead authorizes
            if ($user->designation === 'team_lead' && $request->status === 'authorized') {
                $data['is_personal_lead'] = false;
            }

            if ($lead->lead_type === 'business_loan' && $request->has('business_name')) {
                $data['name'] = $request->business_name;
            }

            // Handle voice recording
            if ($request->hasFile('voice_recording')) {
                if ($lead->voice_recording) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $lead->voice_recording));
                }
                $data['voice_recording'] = '/storage/' . $request->file('voice_recording')->store('voice_recordings', 'public');
                LeadHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => Auth::id(),
                    'action' => 'voice_recording_updated',
                    'status' => $lead->status,
                    'comments' => 'Voice recording updated',
                ]);
            }

            // Log changes
            $changes = [];
            foreach ($data as $key => $value) {
                if (
                    $key !== 'forward_to' && $key !== 'forward_notes' &&
                    $lead->$key != $value && !(is_null($lead->$key) && is_null($value))
                ) {
                    $changes[$key] = [
                        'old' => $lead->$key,
                        'new' => $value,
                    ];
                }
            }

            // Log status change
            if ($request->has('status') && $request->status !== $lead->status) {
                LeadHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => Auth::id(),
                    'action' => 'status_change',
                    'status' => $request->status,
                    'comments' => "Status changed from {$lead->status} to {$request->status}",
                ]);
            }

            // Log forwarding
            if ($request->has('forward_to') && $request->forward_to != $lead->team_lead_id) {
                $data['team_lead_id'] = $request->forward_to;
                LeadHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => Auth::id(),
                    'action' => 'forwarded',
                    'status' => $lead->status,
                    'forwarded_to' => $request->forward_to,
                    'comments' => $request->forward_notes
                        ? "Forwarded to user ID {$request->forward_to}: {$request->forward_notes}"
                        : "Forwarded to user ID {$request->forward_to}",
                ]);
                LeadForwardedHistory::create([
                    'lead_id' => $lead->id,
                    'sender_user_id' => Auth::id(),
                    'receiver_user_id' => $request->forward_to,
                    'is_forwarded' => true,
                    'forwarded_at' => now(),
                ]);
            }

            // Log other changes
            if ($changes) {
                LeadHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => Auth::id(),
                    'action' => 'updated',
                    'status' => $lead->status,
                    'comments' => 'Updated fields: ' . json_encode($changes),
                ]);
            }

            // Update lead
            $lead->update($data);

            $lead->load([
                'employee',
                'teamLead',
                'histories' => function ($query) {
                    $query->orderBy('created_at', 'desc')->with('user');
                },
                'forwardedHistories' => function ($query) {
                    $query->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver']);
                }
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lead updated successfully',
                'data' => $lead,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Soft delete a lead with history
     *
     * @param Lead $lead
     * @return JsonResponse
     */
   public function destroy(Lead $lead): JsonResponse
    {
        try {
            DB::beginTransaction();

            LeadHistory::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id(),
                'action' => 'soft_deleted',
                'status' => $lead->status,
                'comments' => 'Lead soft deleted',
            ]);

            $lead->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lead deleted successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete lead: ' . $e->getMessage(),
            ], 500);
        }
    }

public function restore(int $leadId): JsonResponse
{
    $user = Auth::user();

    try {
        // Fetch the lead (including soft-deleted ones)
        $lead = Lead::withTrashed()->where('id', $leadId)->firstOrFail();

        // Ensure the lead is actually soft-deleted
        if (!$lead->trashed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead is not deleted',
            ], 422);
        }

        DB::beginTransaction();

        // Record restoration in lead history
        LeadHistory::create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'action' => 'restored',
            'status' => $lead->status,
            'comments' => 'Lead restored by ' . $user->name,
        ]);

        // Restore the lead
        $lead->restore();

        // Reload relationships for frontend updates
        $lead->load([
            'employee',
            'teamLead',
            'histories' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            },
            'forwardedHistories' => function ($query) {
                $query->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver']);
            },
        ]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Lead restored successfully',
            'data' => $lead,
        ], 200);

    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to restore lead: ' . $e->getMessage(),
        ], 500);
    }
}



    /**
     * Permanently delete a lead with history
     *
     * @param int $leadId
     * @return JsonResponse
     */
    public function forceDelete(int $leadId): JsonResponse
    {
        $user = Auth::user();
        if ($user->designation !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to permanently delete leads',
            ], 403);
        }

        $lead = Lead::withTrashed()->where('id', $leadId)->firstOrFail();

        try {
            DB::beginTransaction();

            LeadHistory::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id(),
                'action' => 'force_deleted',
                'status' => $lead->status,
                'comments' => 'Lead permanently deleted',
            ]);

            if ($lead->voice_recording) {
                Storage::disk('public')->delete(str_replace('/storage/', '', trim($lead->voice_recording)));
            }

            $lead->forceDelete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lead permanently deleted successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to permanently delete lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Forward a personal lead to a team lead, operations, or admin
     *
     * @param Request $request
     * @param Lead $lead
     * @return JsonResponse
     */
      public function forward(Request $request, Lead $lead): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'forward_to' => 'required|exists:users,id',
            'forward_notes' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate the forward_to user designation
        $forwardToUser = User::find($request->forward_to);
        if (!$forwardToUser || !in_array($forwardToUser->designation, ['team_lead', 'operations', 'admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead can only be forwarded to a team lead, operations, or admin',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update the lead's team_lead_id
            $lead->update(['team_lead_id' => $request->forward_to]);

            // Log in LeadHistory
            LeadHistory::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id(),
                'action' => 'forwarded',
                'status' => $lead->status,
                'forwarded_to' => $request->forward_to,
                'comments' => $request->forward_notes
                    ? "Forwarded to user ID {$request->forward_to}: {$request->forward_notes}"
                    : "Forwarded to user ID {$request->forward_to}",
            ]);

            // Log in LeadForwardedHistory
            LeadForwardedHistory::create([
                'lead_id' => $lead->id,
                'sender_user_id' => Auth::id(),
                'receiver_user_id' => $request->forward_to,
                'is_forwarded' => true,
                'forwarded_at' => now(),
            ]);

            // Load relationships for response
            $lead->load([
                'employee',
                'teamLead',
                'histories' => function ($query) {
                    $query->orderBy('created_at', 'desc')->with('user');
                },
                'forwardedHistories' => function ($query) {
                    $query->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver']);
                }
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lead forwarded successfully',
                'data' => $lead,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to forward lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if a lead is forwarded and return the receiver's ID and designation
     *
     * @param Request $request
     * @param Lead $lead
     * @return JsonResponse
     */
   public function checkForwardStatus(Request $request, Lead $lead): JsonResponse
    {
        // Check if the lead has been forwarded
        $forwardedHistory = $lead->forwardedHistories()
            ->with(['receiver' => function ($query) {
                $query->select('id', 'designation');
            }])
            ->orderBy('forwarded_at', 'desc')
            ->first();

        if (!$forwardedHistory) {
            return response()->json([
                'status' => 'success',
                'message' => 'Lead has not been forwarded',
                'data' => [
                    'is_forwarded' => false,
                    'receiver_user_id' => null,
                    'receiver_designation' => null,
                ],
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lead has been forwarded',
            'data' => [
                'is_forwarded' => true,
                'receiver_user_id' => $forwardedHistory->receiver_user_id,
                'receiver_designation' => $forwardedHistory->receiver->designation,
            ],
        ], 200);
    }
    /**
 * Get a list of future leads with enhanced filtering
 *
 * @param Request $request
 * @return JsonResponse
 */
    public function futureLeads(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Get filter parameters
        $search = $request->query('search');
        $includeDeleted = filter_var($request->query('include_deleted', false), FILTER_VALIDATE_BOOLEAN);
        $expectedMonth = $request->query('expected_month', 'all');

        // Validate expected month
        $validExpectedMonths = ['all', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        if (!in_array($expectedMonth, $validExpectedMonths)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid expected month',
            ], 400);
        }

        // Base query for future leads
        $query = Lead::query()->where('status', 'future_lead');

        // Allow admins to include deleted leads
        if ($includeDeleted && $user->designation === 'admin') {
            $query->withTrashed();
        } elseif ($includeDeleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admins can view deleted leads',
            ], 403);
        }

        // Role-based filtering
        if ($user->designation !== 'team_lead' && $user->designation !== 'operations' && $user->designation !== 'admin') {
            $query->where('employee_id', $user->id);
        } elseif ($user->designation === 'team_lead') {
            $query->where(function ($q) use ($user) {
                $q->where('employee_id', $user->id)
                    ->orWhere('team_lead_id', $user->id);
            });
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Apply expected month filter
        if ($expectedMonth !== 'all') {
            $query->where('expected_month', $expectedMonth);
        }

        // Get future leads with relationships
        $leads = $query->with([
            'employee',
            'teamLead',
            'histories' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            },
            'forwardedHistories' => function ($query) {
                $query->orderBy('forwarded_at', 'desc')->with(['sender', 'receiver']);
            }
        ])->orderBy('created_at', 'desc')->get();

        // Transform leads to include location
        $leads = $leads->map(function ($lead) {
            $lead->location = $lead->location ?: '';
            return $lead;
        });

        // Aggregate data
        $aggregates = [
            'total_future_leads' => [
                'count' => $query->count(),
                'total_amount' => $query->sum('lead_amount') ?? 0,
            ],
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Future leads retrieved successfully',
            'data' => [
                'leads' => $leads,
                'aggregates' => $aggregates,
                'filters_applied' => [
                    'include_deleted' => $includeDeleted,
                    'search' => $search,
                    'expected_month' => $expectedMonth,
                ],
            ],
        ], 200);
    }
public function storeFollowUp(Request $request)
{
    $validated = $request->validate([
        'lead_id'   => 'nullable|exists:leads,id',
        'message'   => 'nullable|string',
        'timestamp' => 'nullable|date',
        'voice_recording' => 'nullable|file|mimes:mp3,wav,aac,m4a,ogg,flac|max:20480',
    ]);

    // Get logged-in user
    $user = auth()->user();

    // Upload if file exists
    $final_voice_recording_path = null;
    if ($request->hasFile('voice_recording')) {
        $path = $request->file('voice_recording')->store('voice_recordings', 'public');
        $final_voice_recording_path = '/storage/' . $path;
    }

    // Save to DB
    $followUp = FollowUp::create([
        'user_id'        => $user->id, // automatically logged-in user
        'lead_id'        => $request->lead_id,
        'message'        => $request->message,
        'recording_path' => $final_voice_recording_path,
        'timestamp'      => $request->timestamp,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Follow up created successfully',
        'data'    => $followUp
    ], 201);
}


 /**
     * Get all follow-ups of logged-in user
     */
    public function getFollowUps()
    {
        $user = auth()->user();

        $followUps = FollowUp::with('lead')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $followUps
        ]);
    }
 /**
     * Get single follow-up by ID
     */
    public function showFollowUp($id)
    {
        $user = auth()->user();

        $followUp = FollowUp::where('user_id', $user->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $followUp
        ]);
    }

    /**
     * Update follow-up
     */
    public function updateFollowUp(Request $request, $id)
    {
        $validated = $request->validate([
            'lead_id'   => 'nullable|exists:leads,id',
            'message'   => 'nullable|string',
            'timestamp' => 'nullable|date',
            'voice_recording' => 'nullable|file|mimes:mp3,wav,aac,m4a,ogg,flac|max:20480',
        ]);

        $user = auth()->user();

        $followUp = FollowUp::where('user_id', $user->id)->findOrFail($id);

        // Upload new file if exists
        if ($request->hasFile('voice_recording')) {
            $path = $request->file('voice_recording')->store('voice_recordings', 'public');
            $followUp->recording_path = '/storage/' . $path;
        }

        // Update fields
        $followUp->update([
            'lead_id'   => $request->lead_id ?? $followUp->lead_id,
            'message'   => $request->message ?? $followUp->message,
            'timestamp' => $request->timestamp ?? $followUp->timestamp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Follow up updated successfully',
            'data'    => $followUp
        ]);
    }

    /**
     * Delete follow-up
     */
    public function deleteFollowUp($id)
    {
        $user = auth()->user();

        $followUp = FollowUp::where('user_id', $user->id)->findOrFail($id);
        $followUp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Follow up deleted successfully'
        ]);
    }

public function getFollowUpsByLead($lead_id)
{
    $user = auth()->user();

    $followUps = FollowUp::with('lead')
        ->where('user_id', $user->id)
        ->where('lead_id', $lead_id)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data'    => $followUps
    ]);
}
    /**
 * Get all soft-deleted leads
 *
 * @return JsonResponse
 */
public function getDeletedLeads(): JsonResponse
{
    try {
        $user = Auth::user();

        // Admin sees all deleted leads
        if ($user->designation === 'admin') {
            $deletedLeadsQuery = Lead::onlyTrashed();
        } else {
            // Employee sees only their own deleted leads
            $deletedLeadsQuery = Lead::onlyTrashed()->where('employee_id', $user->id);
        }

        $deletedLeads = $deletedLeadsQuery
            ->with([
                'employee',
                'teamLead',
                'histories' => function ($query) {
                    $query->orderBy('created_at', 'desc')->with('user');
                },
            ])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted leads retrieved successfully',
            'data' => $deletedLeads,
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch deleted leads: ' . $e->getMessage(),
        ], 500);
    }
}


public function getBankNames(Request $request)
    {
        $banks = Bank::where('is_active', true)
            ->orderBy('bank_name')
            ->get(['id', 'bank_name']);

        return response()->json([
            'status' => true,
            'data' => $banks
        ]);
    }
}
