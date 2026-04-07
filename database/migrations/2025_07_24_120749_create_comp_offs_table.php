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
        Schema::create('comp_offs', function (Blueprint $table) {
              $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->date('worked_on')->nullable();
            $table->date('requested_for')->nullable();

            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->nullable()->default('Pending');

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('expires_on')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'worked_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comp_offs');
    }
};
