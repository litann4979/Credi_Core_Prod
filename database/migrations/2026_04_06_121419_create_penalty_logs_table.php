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
    Schema::create('penalty_logs', function (Blueprint $table) {
        $table->id();

        $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();

        $table->date('date');

        $table->string('type')->nullable(); // late, break, lunch, etc
        $table->integer('minutes')->nullable();
        $table->float('penalty_points')->default(0)->nullable();

        $table->text('description')->nullable()->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalty_logs');
    }
};
