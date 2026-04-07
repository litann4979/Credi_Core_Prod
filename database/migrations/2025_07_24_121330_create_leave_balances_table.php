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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('leave_type')->nullable(); // e.g., casual, sick, earned

            $table->integer('total')->nullable();   // Total leave allotted
            $table->integer('used')->nullable();    // Used leave
            $table->integer('balance')->nullable(); // Remaining balance

            $table->timestamps();

            $table->unique(['user_id', 'leave_type']); // Optional: prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
