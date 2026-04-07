<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    protected $fillable = [
        'user_id', 'month', 'basic', 'hra', 'allowance', 'deductions', 'net_salary', 'pdf_path',

          // New fields
    'employee_code','employee_name','designation','department',
    'joining_date','pan_number','uan_number',

    'basic_salary','conveyance_allowance','medical_allowance',
    'special_allowance','performance_bonus','incentive',
    'overtime_amount','other_earnings','gross_salary',

    'pf_employee','pf_employer','esi_employee','esi_employer',
    'professional_tax','tds','leave_deduction','loan_deduction',
    'other_deductions','total_deductions',

    'working_days','present_days','absent_days',
    'paid_leaves','unpaid_leaves','lwp_days',

    'payment_mode','bank_name','account_number','ifsc_code',
    'payment_date','transaction_reference',

    'payslip_number','status','generated_at','approved_at','paid_at',
    'created_by','approved_by','is_locked','remarks'
    ];

    protected $casts = [
    'month' => 'date:Y-m',
];

public function setMonthAttribute($value)
{
    // If value is only year-month (YYYY-MM), append -01
    if (preg_match('/^\d{4}-\d{2}$/', $value)) {
        $this->attributes['month'] = $value . '-01';
    } else {
        $this->attributes['month'] = $value;
    }
}


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

