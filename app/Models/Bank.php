<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'bank_name',
        'short_name',
        'ifsc_code',
        'branch_name',
        'contact_number',
        'email',
        'address',
        'is_active',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Only active banks
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * (Optional) Relationship example
     * If you link banks to leads/loans later
     */
    // public function leads()
    // {
    //     return $this->hasMany(Lead::class);
    // }
}
