<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('employee_movements', function (Blueprint $table) {
        $table->id();

        $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();

        $table->enum('type', ['work'])->default('work');

        $table->timestamp('start_time')->nullable();
        $table->timestamp('end_time')->nullable();

        $table->integer('allowed_minutes')->default(20);

        $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

        $table->boolean('penalty_applied')->default(false);

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_movements');
    }
};
