<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->date('date'); // ✅ daily score tracking

            $table->integer('total_score')->default(0);
            $table->integer('target_score')->default(0);
            $table->integer('lead_score')->default(0);
            $table->integer('attendance_score')->default(0);
            $table->integer('leave_score')->default(0);

            $table->timestamps();

            // ✅ Foreign key
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // ✅ Composite unique (VERY IMPORTANT)
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
