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
        Schema::table('scores', function (Blueprint $table) {
            $table->decimal('target_score', 10, 2)->default(0)->change();
            $table->decimal('additional_target_score', 10, 2)->default(0)->change();
            $table->decimal('total_score', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->integer('target_score')->default(0)->change();
            $table->integer('additional_target_score')->default(0)->change();
            $table->integer('total_score')->default(0)->change();
        });
    }
};
