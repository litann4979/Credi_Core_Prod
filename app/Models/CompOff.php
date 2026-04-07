<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompOff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'worked_on',
        'requested_for',
        'status',
        'approved_by',
        'applied_to', // ✅ Added
        'expires_on',
    ];

    protected $casts = [
        'worked_on' => 'date',
        'requested_for' => 'date',
        'expires_on' => 'date',
    ];

    // Employee requesting the comp-off
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // HR who approved/rejected
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // HR to whom the request was sent
    public function appliedTo()
    {
        return $this->belongsTo(User::class, 'applied_to');
    }
}
