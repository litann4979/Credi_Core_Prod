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
    Schema::table('attendances', function (Blueprint $table) {
        $table->unsignedBigInteger('team_lead_id')->nullable()->after('employee_id');
        $table->unsignedBigInteger('operation_id')->nullable()->after('team_lead_id');

          // Add foreign key constraints
        $table->foreign('team_lead_id')->references('id')->on('users')->onDelete('set null');
        $table->foreign('operation_id')->references('id')->on('users')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('your_table_name', function (Blueprint $table) {
           $table->dropForeign(['team_lead_id']);
        $table->dropForeign(['operation_id']);
        $table->dropColumn(['team_lead_id', 'operation_id']);
    });
}

};
