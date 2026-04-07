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
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();

            // Employee / Reference
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_code')->nullable();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();

            // Core Salary
            $table->decimal('basic', 10, 2)->nullable();
            $table->decimal('hra', 10, 2)->nullable();
            $table->decimal('allowance', 10, 2)->nullable();
            $table->decimal('deductions', 10, 2)->nullable();

            // Detailed Earnings
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->decimal('conveyance_allowance', 10, 2)->nullable();
            $table->decimal('medical_allowance', 10, 2)->nullable();
            $table->decimal('special_allowance', 10, 2)->nullable();
            $table->decimal('performance_bonus', 10, 2)->nullable();
            $table->decimal('incentive', 10, 2)->nullable();
            $table->decimal('overtime_amount', 10, 2)->nullable();
            $table->decimal('other_earnings', 10, 2)->nullable();
            $table->decimal('gross_salary', 10, 2)->nullable();

            // Deductions
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

            // Payment Defaults
            $table->string('payment_mode')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();

            // Control & Meta
            $table->boolean('is_active')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->text('remarks')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};
