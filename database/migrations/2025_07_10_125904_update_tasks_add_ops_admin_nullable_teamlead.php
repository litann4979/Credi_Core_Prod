<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('operations_id')->nullable()->after('team_lead_id');
            $table->unsignedBigInteger('admin_id')->nullable()->after('operations_id');
            $table->unsignedBigInteger('team_lead_id')->nullable()->change(); // make existing column nullable
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('operations_id');
            $table->dropColumn('admin_id');
            $table->unsignedBigInteger('team_lead_id')->nullable(false)->change(); // revert if needed
        });
    }
};
