<?php

namespace App\Models;

use App\Events\LiveDashboardUpdated;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    /**
     * Set daily discipline pool from office rules when the employee has checked in
     * but attendance_score was never applied (e.g. score row created by leads first).
     */
    public static function applyDefaultAttendancePoolIfNeeded(self $score): bool
    {
        if ((float) $score->attendance_score > 0) {
            return false;
        }

        $rule = OfficeRule::query()->latest('id')->first();
        if (! $rule) {
            return false;
        }

        $hasCheckIn = Attendance::query()
            ->where('employee_id', $score->user_id)
            ->whereDate('date', $score->date)
            ->whereNotNull('check_in')
            ->exists();

        if (! $hasCheckIn) {
            return false;
        }

        $score->attendance_score = (float) ($rule->default_score ?? 0);

        return true;
    }

    protected static function booted(): void
    {
        static::saved(function (Score $score): void {
            event(new LiveDashboardUpdated((int) $score->user_id));
        });
    }

    protected $fillable = [
        'user_id',
        'date',
        'total_score',
        'target_score',
        'lead_score',
        'discipline_score',
        'attendance_score',
        'leave_score',
        'additional_target_score',
        'additional_lead_score',

         // ✅ penalties
         'late_penalty',
         'late_15min_penalty',
         'early_checkout_penalty',
         'break_penalty',
         'lunch_penalty',
         'geofence_penalty',
         'work_penalty',
    ];


    protected $casts = [
        'target_score' => 'float',
        'additional_target_score' => 'float',
        'discipline_score' => 'float',
        'total_score' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
