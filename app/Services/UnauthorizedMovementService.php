<?php

namespace App\Services;

use App\Models\EmployeeMovement;
use App\Models\LocationLog;
use App\Models\OfficeRule;
use App\Models\PenaltyLog;
use App\Models\Score;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UnauthorizedMovementService
{
    private const LUNCH_GRACE_MINUTES = 15;
    private const BREAK_GRACE_MINUTES = 15;

    public function handle(): void
    {
        $rule = OfficeRule::query()->first();
        if (! $rule) {
            return;
        }

        if ((bool) $rule->allow_admin_override) {
            return;
        }

        $penaltyAmount = (float) ($rule->unauthorized_outside_penalty ?? 0);
        if ($penaltyAmount <= 0) {
            return;
        }
        $penaltyWindowMinutes = max(1, (int) ($rule->unauthorized_penalty_window_minutes ?? 15));

        $now = Carbon::now();
        $today = $now->toDateString();

        // During lunch/break + grace, only lunch/break logic should apply.
        if ($this->isWithinProtectedBreakLunchWindow($rule, $now)) {
            return;
        }

        $latestLogs = $this->latestLogPerEmployee($today);

        foreach ($latestLogs as $log) {
            if (! (bool) $log->is_outside) {
                continue;
            }

            $employeeId = (int) $log->employee_id;

            $hasApprovedMovement = EmployeeMovement::query()
                ->where('employee_id', $employeeId)
                ->where('status', 'approved')
                ->where('start_time', '<=', $now)
                ->where(function ($query) use ($now): void {
                    $query->whereNull('end_time')
                        ->orWhere('end_time', '>=', $now);
                })
                ->whereDate('start_time', $today)
                ->exists();

            if ($hasApprovedMovement) {
                continue;
            }

            // Idempotency guard: one geofence penalty per user/day.
            $alreadyPenalizedToday = PenaltyLog::query()
                ->where('employee_id', $employeeId)
                ->where('type', 'unauthorized_outside')
                ->whereDate('date', $today)
                ->exists();

            if ($alreadyPenalizedToday) {
                continue;
            }

            DB::transaction(function () use ($employeeId, $today, $penaltyAmount, $penaltyWindowMinutes): void {
                // Re-check inside transaction for concurrent scheduler safety.
                $todayPenaltyExists = PenaltyLog::query()
                    ->where('employee_id', $employeeId)
                    ->where('type', 'unauthorized_outside')
                    ->whereDate('date', $today)
                    ->lockForUpdate()
                    ->exists();

                if ($todayPenaltyExists) {
                    return;
                }

                $score = Score::query()->firstOrCreate(
                    [
                        'user_id' => $employeeId,
                        'date' => $today,
                    ],
                    [
                        'total_score' => 0,
                        'target_score' => 0,
                        'lead_score' => 0,
                        'discipline_score' => 0,
                        'attendance_score' => 0,
                        'leave_score' => 0,
                        'additional_target_score' => 0,
                        'additional_lead_score' => 0,
                        'late_penalty' => 0,
                        'late_15min_penalty' => 0,
                        'early_checkout_penalty' => 0,
                        'break_penalty' => 0,
                        'lunch_penalty' => 0,
                        'geofence_penalty' => 0,
                        'work_penalty' => 0,
                    ]
                );

                Score::applyDefaultAttendancePoolIfNeeded($score);

                $score->geofence_penalty = (float) $score->geofence_penalty + $penaltyAmount;
                $score->discipline_score = $this->calculateDisciplineScore($score);
                $score->total_score = $this->calculateTotalScore($score);
                $score->save();

                PenaltyLog::query()->create([
                    'employee_id' => $employeeId,
                    'date' => $today,
                    'type' => 'unauthorized_outside',
                    'minutes' => $penaltyWindowMinutes,
                    'penalty_points' => $penaltyAmount,
                    'description' => 'Auto penalty for unauthorized outside movement.',
                ]);
            });
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function latestLogPerEmployee(string $today)
    {
        $subQuery = LocationLog::query()
            ->select('employee_id', DB::raw('MAX(created_at) as latest_created_at'))
            ->whereDate('created_at', $today)
            ->groupBy('employee_id');

        return LocationLog::query()
            ->joinSub($subQuery, 'latest_logs', function ($join): void {
                $join->on('location_logs.employee_id', '=', 'latest_logs.employee_id')
                    ->on('location_logs.created_at', '=', 'latest_logs.latest_created_at');
            })
            ->select('location_logs.employee_id', 'location_logs.is_outside')
            ->get();
    }

    private function calculateDisciplineScore(Score $score): float
    {
        $penalties =
            (float) $score->late_penalty +
            (float) $score->late_15min_penalty +
            (float) $score->early_checkout_penalty +
            (float) $score->break_penalty +
            (float) $score->lunch_penalty +
            (float) $score->geofence_penalty +
            (float) $score->work_penalty;

        $discipline = (float) $score->attendance_score - $penalties;

        return $discipline > 0 ? $discipline : 0;
    }

    private function calculateTotalScore(Score $score): float
    {
        return
            (float) $score->target_score +
            (float) $score->lead_score +
            (float) $score->discipline_score;
    }

    private function isWithinProtectedBreakLunchWindow(OfficeRule $rule, Carbon $now): bool
    {
        $date = $now->toDateString();
        $lunchStart = Carbon::parse($date . ' ' . (string) $rule->lunch_start);
        $lunchEndWithGrace = Carbon::parse($date . ' ' . (string) $rule->lunch_end)
            ->addMinutes(self::LUNCH_GRACE_MINUTES);

        $breakStart = Carbon::parse($date . ' ' . (string) $rule->break_start);
        $breakEndWithGrace = Carbon::parse($date . ' ' . (string) $rule->break_end)
            ->addMinutes(self::BREAK_GRACE_MINUTES);

        return $now->betweenIncluded($lunchStart, $lunchEndWithGrace)
            || $now->betweenIncluded($breakStart, $breakEndWithGrace);
    }
}

