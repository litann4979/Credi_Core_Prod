<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'total_score',
        'target_score',
        'lead_score',
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
        'total_score' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
