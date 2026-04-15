<?php

namespace App\Services;

use App\Models\EmployeeMovement;
use App\Models\LocationLog;
use App\Models\OfficeRule;
use App\Models\Score;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkMovementService
{
    public function handle(): void
    {
        $rule = OfficeRule::query()->first();
        if (! $rule) {
            return;
        }

        // Global admin override enabled means skip automatic penalties.
        if ((bool) $rule->allow_admin_override) {
            return;
        }

        $penaltyAmount = (float) ($rule->work_delay_penalty ?? 0);
        if ($penaltyAmount <= 0) {
            return;
        }

        $now = Carbon::now();

        $movements = EmployeeMovement::query()
            ->where('status', 'approved')
            ->where('penalty_applied', false)
            ->whereNotNull('start_time')
            ->get();

        foreach ($movements as $movement) {
            $startTime = $movement->start_time instanceof Carbon
                ? $movement->start_time
                : Carbon::parse($movement->start_time);

            if ($now->lt($startTime)) {
                continue;
            }

            $elapsedMinutes = $startTime->diffInMinutes($now);
            if ($elapsedMinutes <= (int) $movement->allowed_minutes) {
                continue;
            }

            $latestLog = LocationLog::query()
                ->where('employee_id', $movement->employee_id)
                ->latest('created_at')
                ->first();

            // No GPS logs or already returned inside geofence.
            if (! $latestLog || ! (bool) $latestLog->is_outside) {
                continue;
            }

            DB::transaction(function () use ($movement, $penaltyAmount): void {
                // Lock row to keep penalty idempotent in concurrent scheduler runs.
                $lockedMovement = EmployeeMovement::query()
                    ->whereKey($movement->id)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedMovement || $lockedMovement->penalty_applied) {
                    return;
                }

                $score = Score::query()->firstOrCreate(
                    [
                        'user_id' => $lockedMovement->employee_id,
                        'date' => Carbon::today()->toDateString(),
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

                // Apply work penalty only once per user/day.
                if ((float) $score->work_penalty > 0) {
                    $lockedMovement->penalty_applied = true;
                    $lockedMovement->save();
                    return;
                }

                $score->work_penalty = $penaltyAmount;
                $score->discipline_score = $this->calculateDisciplineScore($score);
                $score->total_score = $this->calculateTotalScore($score);
                $score->save();

                $lockedMovement->penalty_applied = true;
                $lockedMovement->save();
            });
        }
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
}

