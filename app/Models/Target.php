<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'target_value',
        'achieved_value',
        'start_date',
        'end_date',
        'is_completed',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_completed' => 'boolean',
        'target_value' => 'integer',
        'achieved_value' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
