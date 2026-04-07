<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeRule extends Model
{
    protected $fillable = [
        'office_start_time',
        'office_end_time',

        'lunch_start',
        'lunch_end',
        'lunch_allowed_minutes',

        'break_start',
        'break_end',
        'break_allowed_minutes',

        'work_allowed_minutes',

        'geofence_radius',

        'default_score',
        'target_mark',
        'lead_mark',
        'personal_lead_count',

        'late_penalty',
        'late_15min_penalty',
        'unauthorized_outside_penalty',
        'unauthorized_penalty_window_minutes',
        'extra_break_penalty',
        'extra_lunch_penalty',
        'early_checkout_penalty',
        'work_delay_penalty',

        'late_15min_enabled',
        'per_minute_deduction_enabled',
        'penalty_per_minute',

        'allow_admin_override',
    ];

    protected $casts = [
        'late_15min_enabled' => 'boolean',
        'per_minute_deduction_enabled' => 'boolean',
        'allow_admin_override' => 'boolean',
    ];
}
