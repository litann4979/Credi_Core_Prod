<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {

            // Employee Snapshot
            $table->string('employee_code')->nullable()->after('user_id');
            $table->string('employee_name')->nullable()->after('employee_code');
            $table->string('designation')->nullable()->after('employee_name');
            $table->string('department')->nullable()->after('designation');
            $table->date('joining_date')->nullable()->after('department');
            $table->string('pan_number')->nullable()->after('joining_date');
            $table->string('uan_number')->nullable()->after('pan_number');

            // Detailed Earnings
            $table->decimal('basic_salary', 10, 2)->nullable()->after('uan_number');
            $table->decimal('conveyance_allowance', 10, 2)->nullable();
            $table->decimal('medical_allowance', 10, 2)->nullable();
            $table->decimal('special_allowance', 10, 2)->nullable();
            $table->decimal('performance_bonus', 10, 2)->nullable();
            $table->decimal('incentive', 10, 2)->nullable();
            $table->decimal('overtime_amount', 10, 2)->nullable();
            $table->decimal('other_earnings', 10, 2)->nullable();
            $table->decimal('gross_salary', 10, 2)->nullable();

            // Detailed Deductions
            $table->decimal('pf_employee', 10, 2)->nullable();
            $table->decimal('pf_employer', 10, 2)->nullable();
            $table->decimal('esi_employee', 10, 2)->nullable();
            $table->decimal('esi_employer', 10, 2)->nullable();
            $table->decimal('professional_tax', 10, 2)->nullable();
            $table->decimal('tds', 10, 2)->nullable();
            $table->decimal('leave_deduction', 10, 2)->nullable();
            $table->decimal('loan_deduction', 10, 2)->nullable();
            $table->decimal('other_deductions', 10, 2)->nullable();
            $table->decimal('total_deductions', 10, 2)->nullable();

            // Attendance Summary
            $table->integer('working_days')->nullable();
            $table->integer('present_days')->nullable();
            $table->integer('absent_days')->nullable();
            $table->integer('paid_leaves')->nullable();
            $table->integer('unpaid_leaves')->nullable();
            $table->integer('lwp_days')->nullable();

            // Payment Info
            $table->string('payment_mode')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('transaction_reference')->nullable();

            // Payroll Lifecycle
            $table->string('payslip_number')->nullable();
            $table->string('status')->nullable(); // generated, approved, paid
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Audit & Control
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_locked')->nullable();
            $table->text('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {

            $table->dropColumn([
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

                'payslip_number','status','generated_at',
                'approved_at','paid_at',

                'is_locked','remarks'
            ]);

            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['created_by', 'approved_by']);
        });
    }
};
