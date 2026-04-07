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
        Schema::table('attendances', function (Blueprint $table) {
           $table->unsignedBigInteger('hr_id')->nullable()->after('operation_id');

            // If hr_id should be a foreign key referencing users
            $table->foreign('hr_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
             $table->dropForeign(['hr_id']);
            $table->dropColumn('hr_id');
        });
    }
};
