<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $table = 'salary_structures';

    protected $fillable = [
        // Employee / Reference
        'user_id',
        'employee_code',
        'designation',
        'department',

        // Core (legacy support)
        'basic',
        'hra',
        'allowance',
        'deductions',

        // Detailed Earnings
        'basic_salary',
        'conveyance_allowance',
        'medical_allowance',
        'special_allowance',
        'performance_bonus',
        'incentive',
        'overtime_amount',
        'other_earnings',
        'gross_salary',

        // Deductions
        'pf_employee',
        'pf_employer',
        'esi_employee',
        'esi_employer',
        'professional_tax',
        'tds',
        'leave_deduction',
        'loan_deduction',
        'other_deductions',
        'total_deductions',

        // Payment Defaults
        'payment_mode',
        'bank_name',
        'account_number',
        'ifsc_code',

        // Control & Meta
        'is_active',
        'effective_from',
        'effective_to',
        'remarks',

        // Audit
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'effective_from'  => 'date',
        'effective_to'    => 'date',

        'basic'                   => 'decimal:2',
        'hra'                     => 'decimal:2',
        'allowance'               => 'decimal:2',
        'deductions'              => 'decimal:2',

        'basic_salary'             => 'decimal:2',
        'conveyance_allowance'     => 'decimal:2',
        'medical_allowance'        => 'decimal:2',
        'special_allowance'        => 'decimal:2',
        'performance_bonus'        => 'decimal:2',
        'incentive'                => 'decimal:2',
        'overtime_amount'          => 'decimal:2',
        'other_earnings'           => 'decimal:2',
        'gross_salary'             => 'decimal:2',

        'pf_employee'              => 'decimal:2',
        'pf_employer'              => 'decimal:2',
        'esi_employee'             => 'decimal:2',
        'esi_employer'             => 'decimal:2',
        'professional_tax'         => 'decimal:2',
        'tds'                      => 'decimal:2',
        'leave_deduction'          => 'decimal:2',
        'loan_deduction'           => 'decimal:2',
        'other_deductions'         => 'decimal:2',
        'total_deductions'         => 'decimal:2',
    ];

    /* ================================
     | Relationships
     |================================
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* ================================
     | Scopes (Optional but Useful)
     |================================
     */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEffectiveForDate($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('effective_from')
              ->orWhere('effective_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('effective_to')
              ->orWhere('effective_to', '>=', $date);
        });
    }
}
