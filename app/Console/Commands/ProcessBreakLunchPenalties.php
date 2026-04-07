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
        $score = Score::firstOrCreate(
            [
                'user_id' => $employeeId,
                'date' => $date->toDateString(),
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

        $score->break_penalty = (float) ($result['break']['penalty'] ?? 0);
        $score->lunch_penalty = (float) ($result['lunch']['penalty'] ?? 0);
        $this->recalculateTotalScore($score);
        $score->save();

        PenaltyLog::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'date' => $date->toDateString(),
                'type' => 'break',
            ],
            [
                'minutes' => (int) round((float) ($result['break']['outside_minutes'] ?? 0)),
                'penalty_points' => (float) ($result['break']['penalty'] ?? 0),
                'description' => 'Auto-processed from GPS logs (break window).',
            ]
        );

        PenaltyLog::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'date' => $date->toDateString(),
                'type' => 'lunch',
            ],
            [
                'minutes' => (int) round((float) ($result['lunch']['outside_minutes'] ?? 0)),
                'penalty_points' => (float) ($result['lunch']['penalty'] ?? 0),
                'description' => 'Auto-processed from GPS logs (lunch window).',
            ]
        );
    }

    private function recalculateTotalScore(Score $score): void
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

        $score->total_score = $earned - $penalties;
    }
}
