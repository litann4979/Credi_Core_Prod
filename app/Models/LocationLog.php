<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationLog extends Model
{
    protected $fillable = [
        'employee_id',
        'latitude',
        'longitude',
        'distance_from_office',
        'is_outside',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'distance_from_office' => 'float',
        'is_outside' => 'boolean',
    ];

    // 🔗 Relationships
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
