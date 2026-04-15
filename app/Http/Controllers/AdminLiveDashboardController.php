<?php

namespace App\Http\Controllers;

use App\Models\CompOff;
use App\Models\Lead;
use App\Models\Leave;
use App\Models\OfficeRule;
use App\Models\Score;
use App\Models\Target;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLiveDashboardController extends Controller
{
    public function userindex(Request $request)
    {
        $data = $this->buildDashboardData($request);

        return view('admin.LiveDashboard.userindex', $data);
    }

    public function userTvIndex(Request $request)
    {
        $data = $this->buildDashboardData($request);
        $data['dashboardDataUrl'] = route('live-dashboard.user.data');
        $data['tvMode'] = true;

        return response()
            ->view('live-dashboard.user.index', $data)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function dashboardData(Request $request)
    {
        return response()->json($this->buildDashboardData($request));
    }

    public function userTvDashboardData(Request $request)
    {
        return response()
            ->json($this->buildDashboardData($request))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    private function buildDashboardData(Request $request): array
    {
        [$start, $end, $isMonthly] = $this->resolveRange($request);

        $employeeIds = User::query()
            ->where('designation', 'employee')
            ->pluck('id');

        $employeeIdsWithTargets = Target::query()
            ->whereIn('user_id', $employeeIds)
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->distinct()
            ->pluck('user_id');

        $activeEmployeeCount = $employeeIds->count();

        $leadBase = Lead::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('created_at', [$start, $end]);
        $totalLeadCount = (clone $leadBase)->count();
        $totalPositiveLeadAmount = (float) (clone $leadBase)->sum('lead_amount');

        $scoreBase = Score::query()
            ->whereIn('user_id', $employeeIds)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        $averageScore = round((float) (clone $scoreBase)->avg('total_score'), 2);

        $leader = $this->resolveLeader($start, $end);

        $targetAggRows = Target::query()
            ->select(
                'user_id',
                DB::raw('SUM(target_value) as total_target_value'),
                DB::raw('SUM(achieved_value) as total_achieved_value')
            )
            ->whereIn('user_id', $employeeIdsWithTargets)
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $leadAggRows = Lead::query()
            ->select(
                'employee_id',
                DB::raw('COUNT(*) as leads_count'),
                DB::raw('COALESCE(SUM(lead_amount), 0) as leads_amount'),
                DB::raw("COALESCE(SUM(CASE WHEN status = 'disbursed' THEN lead_amount ELSE 0 END), 0) as disbursement_amount")
            )
            ->whereIn('employee_id', $employeeIdsWithTargets)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        $scoreAggRows = Score::query()
            ->select(
                'user_id',
                DB::raw('COALESCE(SUM(total_score), 0) as score_sum'),
                DB::raw('COALESCE(SUM(additional_target_score), 0) as additional_target_sum'),
                DB::raw('COALESCE(SUM(additional_lead_score), 0) as additional_lead_sum')
            )
            ->whereIn('user_id', $employeeIdsWithTargets)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $dailyReferenceDate = $request->filled('date')
            ? Carbon::parse((string) $request->input('date'))->toDateString()
            : Carbon::today()->toDateString();

        $monthReference = $request->filled('month')
            ? Carbon::parse((string) $request->input('month'))
            : ($request->filled('date')
                ? Carbon::parse((string) $request->input('date'))
                : Carbon::today());
        $monthlyStart = $monthReference->copy()->startOfMonth()->toDateString();
        $monthlyEnd = $monthReference->copy()->endOfMonth()->toDateString();

        $dailyScoreRows = Score::query()
            ->select('user_id', DB::raw('COALESCE(SUM(total_score), 0) as daily_score_sum'))
            ->whereIn('user_id', $employeeIdsWithTargets)
            ->whereDate('date', $dailyReferenceDate)
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $monthlyScoreRows = Score::query()
            ->select(
                'user_id',
                DB::raw('COALESCE(SUM(total_score), 0) as monthly_total_score'),
                DB::raw('COALESCE(SUM(additional_target_score), 0) as monthly_additional_target_sum'),
                DB::raw('COALESCE(SUM(additional_lead_score), 0) as monthly_additional_lead_sum')
            )
            ->whereIn('user_id', $employeeIdsWithTargets)
            ->whereBetween('date', [$monthlyStart, $monthlyEnd])
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $rule = OfficeRule::query()->first();
        $targetMark = (float) ($rule->target_mark ?? 0);
        $leadMark = (float) ($rule->lead_mark ?? 0);
        $defaultScore = (float) ($rule->default_score ?? 0);
        $monthlyMaxScore = round(max(0, ($targetMark + $leadMark + $defaultScore) * 25), 2);

        $users = User::query()
            ->whereIn('id', $employeeIdsWithTargets)
            ->get(['id', 'name', 'profile_photo']);

        $employees = $users->map(function (User $user) use ($targetAggRows, $leadAggRows, $scoreAggRows, $dailyScoreRows, $monthlyScoreRows, $isMonthly, $monthlyMaxScore) {
            $targetAgg = $targetAggRows->get($user->id);
            $leadAgg = $leadAggRows->get($user->id);
            $scoreAgg = $scoreAggRows->get($user->id);
            $dailyScoreAgg = $dailyScoreRows->get($user->id);
            $monthlyScoreAgg = $monthlyScoreRows->get($user->id);

            $targetValue = (float) ($targetAgg->total_target_value ?? 0);
            $achievedValue = (float) ($targetAgg->total_achieved_value ?? 0);
            $achievementPercent = $targetValue > 0
                ? min(100, round(($achievedValue / $targetValue) * 100, 2))
                : 0.0;

            $rawScore = (float) ($scoreAgg->score_sum ?? 0);
            $additionalTargetSum = (float) ($scoreAgg->additional_target_sum ?? 0);
            $additionalLeadSum = (float) ($scoreAgg->additional_lead_sum ?? 0);

            $scoreValue = round($rawScore, 2);
            $scoreDisplay = number_format($scoreValue, 1);
            $scoreForStatus = $rawScore;

            if ($isMonthly) {
                $monthlyScore = $rawScore + $additionalTargetSum + $additionalLeadSum;
                $finalScore = $monthlyMaxScore > 0 && $monthlyScore > $monthlyMaxScore
                    ? ($monthlyScore - $monthlyMaxScore)
                    : $monthlyScore;

                $scoreValue = round($finalScore, 2);
                $scoreDisplay = number_format($scoreValue, 2) . ' / ' . number_format($monthlyMaxScore, 2);
                $scoreForStatus = $monthlyMaxScore > 0
                    ? min(100, round(($scoreValue / $monthlyMaxScore) * 100, 2))
                    : 0.0;
            }

            $dailyScoreValue = round((float) ($dailyScoreAgg->daily_score_sum ?? 0), 2);

            $monthlyTotalScore = (float) ($monthlyScoreAgg->monthly_total_score ?? 0);
            $monthlyAdditionalTarget = (float) ($monthlyScoreAgg->monthly_additional_target_sum ?? 0);
            $monthlyAdditionalLead = (float) ($monthlyScoreAgg->monthly_additional_lead_sum ?? 0);
            $monthlyScore = $monthlyTotalScore + $monthlyAdditionalTarget + $monthlyAdditionalLead;
            $monthlyFinalScore = $monthlyMaxScore > 0 && $monthlyScore > $monthlyMaxScore
                ? ($monthlyScore - $monthlyMaxScore)
                : $monthlyScore;
            $monthlyScoreDisplay = number_format(round($monthlyFinalScore, 2), 2) . ' / ' . number_format($monthlyMaxScore, 2);

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'profile_photo' => $user->profile_photo,
                'positive_leads_count' => (int) ($leadAgg->leads_count ?? 0),
                'positive_leads_amount' => round((float) ($leadAgg->leads_amount ?? 0), 2),
                'disbursement_amount' => round((float) ($leadAgg->disbursement_amount ?? 0), 2),
                'target_achievement_percent' => $achievementPercent,
                'score' => $scoreValue,
                'score_display' => $scoreDisplay,
                'daily_score' => $dailyScoreValue,
                'daily_score_display' => number_format($dailyScoreValue, 2),
                'monthly_score' => round($monthlyFinalScore, 2),
                'monthly_score_display' => $monthlyScoreDisplay,
                'monthly_max_score' => $monthlyMaxScore,
                'score_percent_for_status' => round($scoreForStatus, 2),
                'status' => $this->mapStatus((float) $scoreForStatus),
            ];
        })
        ->sortByDesc('score')
        ->values()
        ->map(function (array $row, int $index) {
            $row['rank'] = $index + 1;
            return $row;
        });

        $totalAchieved = (float) $targetAggRows->sum('total_achieved_value');
        $totalTarget = (float) $targetAggRows->sum('total_target_value');
        $targetAchievementPercent = $totalTarget > 0
            ? min(100, round(($totalAchieved / $totalTarget) * 100, 2))
            : 0.0;

        $personalLeadCount = (float) ($rule->personal_lead_count ?? 0);
        $leadEntryDenominator = $personalLeadCount * max(1, $activeEmployeeCount);
        $leadEntryPercent = $leadEntryDenominator > 0
            ? round(($totalLeadCount / $leadEntryDenominator) * 100, 2)
            : 0.0;

        $disciplineSum = (float) (clone $scoreBase)->sum('discipline_score');
        $maxDisciplinePerEmployee = $isMonthly ? 2500 : 100;
        $disciplineDenominator = $maxDisciplinePerEmployee * max(1, $activeEmployeeCount);
        $disciplinePercent = $disciplineDenominator > 0
            ? round(($disciplineSum / $disciplineDenominator) * 100, 2)
            : 0.0;

        $approvedLeaves = Leave::query()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString());

        $leaveRows = (clone $approvedLeaves)
            ->select('leave_type', DB::raw('COUNT(*) as total'))
            ->groupBy('leave_type')
            ->get();

        $normalizedLeaveCounts = [
            'casual leave' => 0,
            'sick leave' => 0,
            'maternity leave' => 0,
        ];

        foreach ($leaveRows as $row) {
            $type = strtolower(trim((string) $row->leave_type));
            if (isset($normalizedLeaveCounts[$type])) {
                $normalizedLeaveCounts[$type] += (int) $row->total;
            }
        }

        $compOffApprovedCount = CompOff::query()
            ->whereRaw('LOWER(status) = ?', ['approved'])
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $kpis = [
            'total_lead_entry_count' => $totalLeadCount,
            'positive_lead_amount' => round($totalPositiveLeadAmount, 2),
            'average_score' => $averageScore,
            'leader' => $leader,
        ];

        $performanceMetrics = [
            'target_achievement_percent' => $targetAchievementPercent,
            'lead_entry_percent' => $leadEntryPercent,
            'discipline_percent' => $disciplinePercent,
        ];

        $leaveSummary = [
            'casual_leave' => (int) $normalizedLeaveCounts['casual leave'],
            'sick_leave' => (int) $normalizedLeaveCounts['sick leave'],
            'maternity_leave' => (int) $normalizedLeaveCounts['maternity leave'],
            'approved_comp_offs' => (int) $compOffApprovedCount,
        ];

        return compact(
            'kpis',
            'employees',
            'performanceMetrics',
            'leaveSummary'
        );
    }
    public function index(Request $request)
    {
        $data = $this->buildDashboardData($request);

        return view('admin.LiveDashboard.index', $data);
    }

    public function teamLeadIndex(Request $request)
    {
        $data = $this->buildDashboardData($request);

        return view('admin.LiveDashboard.TeamLeadIndex', $data);
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: bool}
     */
    private function resolveRange(Request $request): array
    {
        if ($request->filled('month')) {
            $start = Carbon::parse((string) $request->input('month'))->startOfMonth();
            $end = Carbon::parse((string) $request->input('month'))->endOfMonth()->endOfDay();
            return [$start, $end, true];
        }

        $day = $request->filled('date')
            ? Carbon::parse((string) $request->input('date'))
            : Carbon::today();

        return [$day->copy()->startOfDay(), $day->copy()->endOfDay(), false];
    }

    /**
     * @return array{name: string|null, profile_photo: string|null}
     */
    private function resolveLeader(Carbon $start, Carbon $end): array
    {
        $leader = User::query()
            ->select('users.id', 'users.name', 'users.profile_photo')
            ->join('users as employees', 'employees.team_lead_id', '=', 'users.id')
            ->leftJoin('scores', function ($join) use ($start, $end): void {
                $join->on('scores.user_id', '=', 'employees.id')
                    ->whereBetween('scores.date', [$start->toDateString(), $end->toDateString()]);
            })
            ->where('users.designation', 'team_lead')
            ->groupBy('users.id', 'users.name', 'users.profile_photo')
            ->orderByDesc(DB::raw('COALESCE(AVG(scores.total_score), 0)'))
            ->first();

        return [
            'name' => $leader?->name,
            'profile_photo' => $leader?->profile_photo,
        ];
    }

    private function mapStatus(float $score): string
    {
        if ($score >= 90) {
            return 'Excellent';
        }
        if ($score >= 70) {
            return 'Good';
        }
        if ($score >= 50) {
            return 'Average';
        }

        return 'Low';
    }
}
