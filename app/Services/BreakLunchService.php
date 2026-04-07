<?php

namespace App\Services;

use App\Models\LocationLog;
use App\Models\OfficeRule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BreakLunchService
{
    /**
     * Calculate break/lunch outside minutes and penalties for an employee/date.
     *
     * @return array{
     *   break: array{outside_minutes: float, penalty: float},
     *   lunch: array{outside_minutes: float, penalty: float}
     * }
     */
    public function calculateForEmployeeDate(int $employeeId, Carbon|string $date): array
    {
        $day = $date instanceof Carbon
            ? $date->copy()->startOfDay()
            : Carbon::parse($date)->startOfDay();

        $rule = OfficeRule::query()->latest('id')->first();
        if (! $rule) {
            return $this->emptyResult();
        }

        $logs = LocationLog::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('created_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])
            ->orderBy('created_at')
            ->get(['id', 'employee_id', 'is_outside', 'created_at']);

        if ($logs->isEmpty()) {
            return $this->emptyResult();
        }

        $now = Carbon::now();
        $breakWindow = [
            'start' => $this->windowAt($day, (string) $rule->break_start),
            'end' => $this->windowAt($day, (string) $rule->break_end),
        ];
        $lunchWindow = [
            'start' => $this->windowAt($day, (string) $rule->lunch_start),
            'end' => $this->windowAt($day, (string) $rule->lunch_end),
        ];

        $breakOutside = $this->outsideMinutesWithinWindow($logs, $breakWindow['start'], $breakWindow['end'], $now);
        $lunchOutside = $this->outsideMinutesWithinWindow($logs, $lunchWindow['start'], $lunchWindow['end'], $now);

        $breakPenalty = $this->computeWindowPenalty(
            outsideMinutes: $breakOutside,
            allowedMinutes: (int) ($rule->break_allowed_minutes ?? 0),
            fixedPenalty: (float) ($rule->extra_break_penalty ?? 0),
            perMinuteEnabled: (bool) ($rule->per_minute_deduction_enabled ?? false),
            penaltyPerMinute: (float) ($rule->penalty_per_minute ?? 0),
            currentlyPastWindowEndOutside: $this->isOutsideAfterWindowEnd($logs, $breakWindow['end'], $now)
        );

        $lunchPenalty = $this->computeWindowPenalty(
            outsideMinutes: $lunchOutside,
            allowedMinutes: (int) ($rule->lunch_allowed_minutes ?? 0),
            fixedPenalty: (float) ($rule->extra_lunch_penalty ?? 0),
            perMinuteEnabled: (bool) ($rule->per_minute_deduction_enabled ?? false),
            penaltyPerMinute: (float) ($rule->penalty_per_minute ?? 0),
            currentlyPastWindowEndOutside: $this->isOutsideAfterWindowEnd($logs, $lunchWindow['end'], $now)
        );

        return [
            'break' => [
                'outside_minutes' => round($breakOutside, 2),
                'penalty' => round($breakPenalty, 2),
            ],
            'lunch' => [
                'outside_minutes' => round($lunchOutside, 2),
                'penalty' => round($lunchPenalty, 2),
            ],
        ];
    }

    private function emptyResult(): array
    {
        return [
            'break' => ['outside_minutes' => 0.0, 'penalty' => 0.0],
            'lunch' => ['outside_minutes' => 0.0, 'penalty' => 0.0],
        ];
    }

    private function windowAt(Carbon $day, string $time): Carbon
    {
        return Carbon::parse($day->format('Y-m-d') . ' ' . $time);
    }

    private function outsideMinutesWithinWindow(
        Collection $logs,
        Carbon $windowStart,
        Carbon $windowEnd,
        Carbon $now
    ): float {
        if ($windowEnd->lessThanOrEqualTo($windowStart)) {
            return 0.0;
        }

        $totalMinutes = 0.0;
        $count = $logs->count();

        for ($i = 0; $i < $count; $i++) {
            $current = $logs[$i];
            if (! (bool) $current->is_outside) {
                continue;
            }

            $currentTime = Carbon::parse($current->created_at);
            $nextTime = $i + 1 < $count
                ? Carbon::parse($logs[$i + 1]->created_at)
                : $now->copy();

            $segmentStart = $currentTime->greaterThan($windowStart) ? $currentTime : $windowStart->copy();
            $segmentEnd = $nextTime->lessThan($windowEnd) ? $nextTime : $windowEnd->copy();

            if ($segmentEnd->greaterThan($segmentStart)) {
                $totalMinutes += $segmentStart->diffInSeconds($segmentEnd) / 60;
            }
        }

        return $totalMinutes;
    }

    private function computeWindowPenalty(
        float $outsideMinutes,
        int $allowedMinutes,
        float $fixedPenalty,
        bool $perMinuteEnabled,
        float $penaltyPerMinute,
        bool $currentlyPastWindowEndOutside
    ): float {
        $extraMinutes = max(0, $outsideMinutes - max(0, $allowedMinutes));
        $penalty = 0.0;

        if ($extraMinutes > 0) {
            $penalty += $perMinuteEnabled
                ? ($extraMinutes * max(0, $penaltyPerMinute))
                : max(0, $fixedPenalty);
        }

        if ($currentlyPastWindowEndOutside) {
            $penalty += max(0, $fixedPenalty);
        }

        return $penalty;
    }

    private function isOutsideAfterWindowEnd(Collection $logs, Carbon $windowEnd, Carbon $now): bool
    {
        if ($now->lessThanOrEqualTo($windowEnd) || $logs->isEmpty()) {
            return false;
        }

        $last = $logs->last();
        return (bool) ($last->is_outside ?? false);
    }

}
