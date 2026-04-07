<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
   use HasFactory;

    protected $fillable = [
        'employee_id',
        'team_lead_id',
        'operation_id',
        'hr_id',
        'date',
        'check_in',
        'late_minutes',
        'check_out',
        'early_checkout_minutes',
        'check_in_location',
        'check_out_location',
        'check_in_coordinates',
        'check_out_coordinates',
        'notes',
        'checkin_image',
        'checkout_image',
        'last_location_update',
        'is_within_geofence',
        'reason',
        'sessions'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'last_location_update' => 'datetime',
        'is_within_geofence' => 'boolean',
        'sessions' => 'array'
    ];

    // Employee associated with this attendance record
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

     public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    public function operation()
    {
        return $this->belongsTo(User::class, 'operation_id');
    }
      public function hr()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }
}
