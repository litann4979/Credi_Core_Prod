<?php

namespace App\Console\Commands;

use App\Models\LocationLog;
use App\Models\PenaltyLog;
use App\Models\Score;
use App\Services\BreakLunchService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessBreakLunchPenalties extends Command
{
    protected $signature = 'penalties:process-break-lunch {--date=} {--employee_id=}';
    protected $description = 'Process break/lunch penalties from live location logs';

    public function handle(BreakLunchService $service): int
    {
        $date = $this->option('date')
            ? Carbon::parse((string) $this->option('date'))->startOfDay()
            : Carbon::today();

        $employeeOption = $this->option('employee_id');
        $employeeIds = $employeeOption
            ? [(int) $employeeOption]
            : LocationLog::query()
                ->whereDate('created_at', $date->toDateString())
                ->distinct()
                ->pluck('employee_id')
                ->map(fn ($id) => (int) $id)
                ->all();

        if (empty($employeeIds)) {
            $this->info('No location logs found for processing.');
            return Command::SUCCESS;
        }

        foreach ($employeeIds as $employeeId) {
            DB::beginTransaction();
            try {
                $result = $service->calculateForEmployeeDate($employeeId, $date);
                $this->applyScoreAndLogs($employeeId, $date, $result);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Break/Lunch penalty processing failed.', [
                    'employee_id' => $employeeId,
                    'date' => $date->toDateString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Break/Lunch penalty processing completed.');
        return Command::SUCCESS;
    }

    /**
     * @param array{
     *   break: array{outside_minutes: float, penalty: float},
     *   lunch: array{outside_minutes: float, penalty: float}
     * } $result
     */
    private function applyScoreAndLogs(int $employeeId, Carbon $date, array $result): void
    {
        $dateString = $date->toDateString();
        $score = Score::firstOrCreate(
            [
                'user_id' => $employeeId,
                'date' => $dateString,
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

        $poolSynced = Score::applyDefaultAttendancePoolIfNeeded($score);

        $breakPenalty = (float) ($result['break']['penalty'] ?? 0);
        $lunchPenalty = (float) ($result['lunch']['penalty'] ?? 0);
        $scoreChanged = false;

        // Apply break penalty only once per user/day.
        if ((float) $score->break_penalty <= 0 && $breakPenalty > 0) {
            $score->break_penalty = $breakPenalty;
            $scoreChanged = true;

            PenaltyLog::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $dateString,
                    'type' => 'break',
                ],
                [
                    'minutes' => (int) round((float) ($result['break']['outside_minutes'] ?? 0)),
                    'penalty_points' => $breakPenalty,
                    'description' => 'Auto-processed from GPS logs (break window).',
                ]
            );
        }

        // Apply lunch penalty only once per user/day.
        if ((float) $score->lunch_penalty <= 0 && $lunchPenalty > 0) {
            $score->lunch_penalty = $lunchPenalty;
            $scoreChanged = true;

            PenaltyLog::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $dateString,
                    'type' => 'lunch',
                ],
                [
                    'minutes' => (int) round((float) ($result['lunch']['outside_minutes'] ?? 0)),
                    'penalty_points' => $lunchPenalty,
                    'description' => 'Auto-processed from GPS logs (lunch window).',
                ]
            );
        }

        if ($scoreChanged || $poolSynced) {
            $this->recalculateDisciplineScore($score);
            $this->recalculateTotalScore($score);
            $score->save();
        }
    }

    private function recalculateDisciplineScore(Score $score): void
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
        $score->discipline_score = $discipline > 0 ? $discipline : 0;
    }

    private function recalculateTotalScore(Score $score): void
    {
        $score->total_score =
            (float) $score->target_score +
            (float) $score->lead_score +
            (float) $score->discipline_score +
            (float) $score->leave_score +
            (float) $score->additional_target_score +
            (float) $score->additional_lead_score;
    }
}
