<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_rules', function (Blueprint $table) {
            $table->unsignedInteger('unauthorized_penalty_window_minutes')
                ->default(15)
                ->after('unauthorized_outside_penalty');
        });
    }

    public function down(): void
    {
        Schema::table('office_rules', function (Blueprint $table) {
            $table->dropColumn('unauthorized_penalty_window_minutes');
        });
    }
};

