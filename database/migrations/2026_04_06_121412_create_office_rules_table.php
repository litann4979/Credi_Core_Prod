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
        Schema::create('office_rules', function (Blueprint $table) {
            $table->id();

            // 🕒 Office Timing
            $table->time('office_start_time')->default('09:30:00');
            $table->time('office_end_time')->default('18:30:00');

            // 🍱 Lunch
            $table->time('lunch_start')->default('13:00:00');
            $table->time('lunch_end')->default('13:30:00');
            $table->integer('lunch_allowed_minutes')->default(30);

            // ☕ Break
            $table->time('break_start')->default('16:00:00');
            $table->time('break_end')->default('16:30:00');
            $table->integer('break_allowed_minutes')->default(15);

            // 🚶 Work Outside
            $table->integer('work_allowed_minutes')->default(20);

            // 📍 Geofence
            $table->float('geofence_radius')->default(50);

            // 🎯 Score System
            $table->integer('default_score')->default(50);
            $table->integer('target_mark')->default(10);
            $table->integer('lead_mark')->default(5);
            $table->integer('personal_lead_count')->default(10);

            // ❌ Penalties
            $table->integer('late_penalty')->default(5);
            $table->integer('late_15min_penalty')->default(10);
            $table->integer('unauthorized_outside_penalty')->default(10);
            $table->integer('extra_break_penalty')->default(10);
            $table->integer('extra_lunch_penalty')->default(10);
            $table->integer('early_checkout_penalty')->default(10);
            $table->integer('work_delay_penalty')->default(10);

            // ⚙️ Config
            $table->boolean('late_15min_enabled')->default(true);
            $table->boolean('per_minute_deduction_enabled')->default(false);
            $table->integer('penalty_per_minute')->default(1);

            // 🛠 Admin control
            $table->boolean('allow_admin_override')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_rules');
    }
};
