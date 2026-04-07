<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Get filter parameters
        $leadType = $request->query('lead_type', 'all');
        $status = $request->query('status', 'all');
        $expectedMonth = $request->query('expected_month', Carbon::now()->format('F')); // e.g., 'July'
        $currentYear = Carbon::now()->year; // e.g., 2025

        // Validate filter parameters
        $validLeadTypes = ['all', 'personal_loan', 'business_loan', 'home_loan', 'creditcard_loan'];
        $validStatuses = ['all', 'personal_lead', 'authorized', 'login', 'approved', 'disbursed', 'rejected', 'future_lead'];
        $validExpectedMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        if (!in_array($leadType, $validLeadTypes)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid lead type',
            ], 400);
        }

        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid status',
            ], 400);
        }

        if (!in_array($expectedMonth, $validExpectedMonths)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid expected month',
            ], 400);
        }

        // Base query based on user role
        $query = Lead::query();
        if ($user->designation !== 'team_lead') {
            $query->where('employee_id', $user->id);
        } else {
            $query->where(function ($q) use ($user) {
                $q->where('employee_id', $user->id)
                  ->orWhere('team_lead_id', $user->id);
            });
        }

        // Total leads (all leads excluding future_lead and creditcard_loan by default, specific lead_type when selected, all time)
        $totalLeadsQuery = $query->clone();
        $totalLeadsQuery->where('status', '!=', 'future_lead')
                        ->whereIn('lead_type', ['personal_loan', 'business_loan', 'home_loan']);

        if ($leadType !== 'all' && $leadType !== 'creditcard_loan') {
            $totalLeadsQuery->where('lead_type', $leadType);
        } elseif ($leadType === 'creditcard_loan') {
            $totalLeadsQuery->where('lead_type', 'invalid'); // Force zero results for creditcard_loan
        }

        $totalLeads = [
            'count' => $totalLeadsQuery->count(),
            'total_amount' => number_format($totalLeadsQuery->sum('lead_amount') ?? 0, 2, '.', ''),
        ];

        // Per lead_type totals (all time, excluding future_lead and creditcard_loan)
        $leadTypesForAggregates = ['personal_loan', 'business_loan', 'home_loan'];
        $perTypeTotals = [];
        foreach ($leadTypesForAggregates as $type) {
            $typeTotalQuery = $query->clone();
            $typeTotalQuery->where('lead_type', $type)
                           ->where('status', '!=', 'future_lead');

            $perTypeTotals[$type] = [
                'count' => $typeTotalQuery->count(),
                'total_amount' => number_format($typeTotalQuery->sum('lead_amount') ?? 0, 2, '.', ''),
            ];
        }

        // Apply current year filter to main query
        $query->whereYear('created_at', $currentYear);

        // Apply expected month filter
        $query->where('expected_month', $expectedMonth);

        // Apply lead type filter
        if ($leadType !== 'all') {
            $query->where('lead_type', $leadType);
        }

        // Apply status filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Get status aggregates (filtered by current year, expected month, lead_type, status)
        $personalLeads = [
            'count' => $query->clone()->where('status', 'personal_lead')->whereNotIn('lead_type', ['creditcard_loan'])->count(),
            'total_amount' => number_format($query->clone()->where('status', 'personal_lead')->whereNotIn('lead_type', ['creditcard_loan'])->sum('lead_amount') ?? 0, 2, '.', ''),
        ];

        $authorizedLeads = [
            'count' => $query->clone()->where('status', 'authorized')->count(),
            'total_amount' => number_format($query->clone()->where('status', 'authorized')->sum('lead_amount') ?? 0, 2, '.', ''),
        ];

        $loginLeads = [
            'count' => $query->clone()->where('status', 'login')->count(),
            'total_amount' => number_format($query->clone()->where('status', 'login')->sum('lead_amount') ?? 0, 2, '.', ''),
        ];

        $approvedLeads = [
            'count' => $query->clone()->where('status', 'approved')->count(),
            'total_amount' => number_format($query->clone()->where('status', 'approved')->sum('lead_amount') ?? 0, 2, '.', ''),
        ];

        $disbursedLeads = [
            'count' => $query->clone()->where('status', 'disbursed')->count(),
            'total_amount' => number_format($query->clone()->where('status', 'disbursed')->sum('lead_amount') ?? 0, 2, '.', ''),
        ];

        $rejectedLeads = [
            'count' => $query->clone()->where('status', 'rejected')->count(),
            'total_amount' => number_format($query->clone()->where('status', 'rejected')->sum('lead_amount') ?? 0, 2, '.', ''),
        ];

     $futureLeadsQuery = Lead::query();
if ($user->designation !== 'team_lead') {
    $futureLeadsQuery->where('employee_id', $user->id);
} else {
    $futureLeadsQuery->where(function ($q) use ($user) {
        $q->where('employee_id', $user->id)
          ->orWhere('team_lead_id', $user->id);
    });
}
$futureLeadsQuery->where('status', 'future_lead');

        $futureLeads = [
            'count' => $futureLeadsQuery->count(),
            'total_amount' => number_format($futureLeadsQuery->sum('lead_amount') ?? 0, 2, '.', ''),
        ];

        // Credit card statistics (for lead_type = creditcard_loan, all time, without year/month/status filters, including future_lead)
        $creditCardQuery = Lead::query();
        if ($user->designation !== 'team_lead') {
            $creditCardQuery->where('employee_id', $user->id);
        } else {
            $creditCardQuery->where(function ($q) use ($user) {
                $q->where('employee_id', $user->id)
                  ->orWhere('team_lead_id', $user->id);
            });
        }
        $creditCardQuery->where('lead_type', 'creditcard_loan');

        $creditCardStatistics = [
            'ongoing' => [
                'count' => $creditCardQuery->clone()->where('status', 'personal_lead')->count(),
            ],
            'approved' => [
                'count' => $creditCardQuery->clone()->where('status', 'approved')->count(),
            ],
            'rejected' => [
                'count' => $creditCardQuery->clone()->where('status', 'rejected')->count(),
            ],
            'future' => [
                'count' => $creditCardQuery->clone()->where('status', 'future_lead')->count(),
            ],
        ];

        // All leads list (filtered by current year, expected month, lead_type, status)
        $allLeads = $query->clone()->with(['employee'])->get()->map(function ($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->name,
                'lead_type' => $lead->lead_type,
                'lead_amount' => $lead->lead_amount ? number_format($lead->lead_amount, 2, '.', '') : null,
                'status' => $lead->status,
                'expected_month' => $lead->expected_month,
                'created_at' => $lead->created_at->toISOString(),
                'location' => implode(', ', array_filter([
                    $lead->city,
                    $lead->district,
                    $lead->state,
                ], function ($value) {
                    return !is_null($value) && $value !== '';
                })),
                'employee' => [
                    'name' => $lead->employee ? $lead->employee->name : null,
                    'profile_photo_url' => $lead->employee ? $lead->employee->profile_photo_url : null,
                    'pan_card_url' => $lead->employee ? $lead->employee->pan_card_url : null,
                    'aadhar_card_url' => $lead->employee ? $lead->employee->aadhar_card_url : null,
                    'signature_url' => $lead->employee ? $lead->employee->signature_url : null,
                ],
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard data retrieved successfully',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'designation' => $user->designation,
                  'profile_photo' => $user->profile_photo ? asset($user->profile_photo) : null,
                ],
                'aggregates' => [
                    'total_leads' => $totalLeads,
                    'personal_loan' => $perTypeTotals['personal_loan'] ?? ['count' => 0, 'total_amount' => '0.00'],
                    'business_loan' => $perTypeTotals['business_loan'] ?? ['count' => 0, 'total_amount' => '0.00'],
                    'home_loan' => $perTypeTotals['home_loan'] ?? ['count' => 0, 'total_amount' => '0.00'],
                    'personal_leads' => $personalLeads,
                    'authorized_leads' => $authorizedLeads,
                    'login_leads' => $loginLeads,
                    'approved_leads' => $approvedLeads,
                    'disbursed_leads' => $disbursedLeads,
                    'rejected_leads' => $rejectedLeads,
                ],
                'future_leads' => $futureLeads,
                'creditcard_statistics' => $creditCardStatistics,
                'all_leads' => $allLeads,
                'filters_applied' => [
                    'lead_type' => $leadType,
                    'status' => $status,
                    'year' => $currentYear,
                    'expected_month' => $expectedMonth,
                ],
            ],
        ], 200);
    }
}
