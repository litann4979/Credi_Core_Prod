<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table): void {
            $table->unsignedBigInteger('target_id')
                ->nullable()
                ->after('salary_slip_id');

            $table->foreign('target_id')
                ->references('id')
                ->on('targets')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table): void {
            $table->dropForeign(['target_id']);
            $table->dropColumn('target_id');
        });
    }
};
