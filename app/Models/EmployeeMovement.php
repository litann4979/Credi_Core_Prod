<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeMovement extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'start_time',
        'end_time',
        'allowed_minutes',
        'approved_by',
        'status',
        'penalty_applied',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'penalty_applied' => 'boolean',
    ];

    // 🔗 Relationships
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
