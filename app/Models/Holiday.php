<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'date',
    ];

    // Cast fields to proper types
    protected $casts = [
        'date' => 'date',
        'deleted_at' => 'datetime',
    ];
}
