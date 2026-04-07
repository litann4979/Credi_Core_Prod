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
        Schema::create('task_user', function (Blueprint $table) {
              $table->id();

            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->enum('status', ['pending', 'in_progress', 'completed'])->nullable();
            $table->tinyInteger('progress')->unsigned()->nullable();
             $table->text('message')->nullable(); 
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_user');
    }
};
