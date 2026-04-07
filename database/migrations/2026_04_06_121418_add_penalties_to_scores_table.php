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
        Schema::table('scores', function (Blueprint $table) {

            // ❌ Penalties
            $table->float('late_penalty')->default(0);
            $table->float('late_15min_penalty')->default(0);
            $table->float('early_checkout_penalty')->default(0);

            $table->float('break_penalty')->default(0);
            $table->float('lunch_penalty')->default(0);

            $table->float('geofence_penalty')->default(0);
            $table->float('work_penalty')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            //
        });
    }
};
