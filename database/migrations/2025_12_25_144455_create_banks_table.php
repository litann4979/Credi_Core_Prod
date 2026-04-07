<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();

            // Bank basic info
            $table->string('bank_name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('branch_name')->nullable();

            // Contact / Address
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
