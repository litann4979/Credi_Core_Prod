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
        $windowStart = $now->copy()->subMinutes($penaltyWindowMinutes);

        $latestLogs = $this->latestLogPerEmployee();

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
                ->exists();

            if ($hasApprovedMovement) {
                continue;
            }

            $alreadyPenalizedRecently = PenaltyLog::query()
                ->where('employee_id', $employeeId)
                ->where('type', 'unauthorized_outside')
                ->where('created_at', '>=', $windowStart)
                ->exists();

            if ($alreadyPenalizedRecently) {
                continue;
            }

            DB::transaction(function () use ($employeeId, $today, $penaltyAmount, $penaltyWindowMinutes): void {
                // Re-check inside transaction to keep penalty idempotent.
                $recentPenaltyExists = PenaltyLog::query()
                    ->where('employee_id', $employeeId)
                    ->where('type', 'unauthorized_outside')
                    ->where('created_at', '>=', Carbon::now()->subMinutes($penaltyWindowMinutes))
                    ->lockForUpdate()
                    ->exists();

                if ($recentPenaltyExists) {
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

                $score->geofence_penalty = (float) $score->geofence_penalty + $penaltyAmount;
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
    private function latestLogPerEmployee()
    {
        $subQuery = LocationLog::query()
            ->select('employee_id', DB::raw('MAX(created_at) as latest_created_at'))
            ->groupBy('employee_id');

        return LocationLog::query()
            ->joinSub($subQuery, 'latest_logs', function ($join): void {
                $join->on('location_logs.employee_id', '=', 'latest_logs.employee_id')
                    ->on('location_logs.created_at', '=', 'latest_logs.latest_created_at');
            })
            ->select('location_logs.employee_id', 'location_logs.is_outside')
            ->get();
    }

    private function calculateTotalScore(Score $score): float
    {
        $earned =
            (float) $score->target_score +
            (float) $score->lead_score +
            (float) $score->attendance_score +
            (float) $score->leave_score +
            (float) $score->additional_target_score +
            (float) $score->additional_lead_score;

        $penalties =
            (float) $score->late_penalty +
            (float) $score->late_15min_penalty +
            (float) $score->early_checkout_penalty +
            (float) $score->break_penalty +
            (float) $score->lunch_penalty +
            (float) $score->geofence_penalty +
            (float) $score->work_penalty;

        return $earned - $penalties;
    }
}

