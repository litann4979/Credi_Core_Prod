<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltyLog extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'type',
        'minutes',
        'penalty_points',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'penalty_points' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
