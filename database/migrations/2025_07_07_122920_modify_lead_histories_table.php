<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop lead_forwarded_histories table
        Schema::dropIfExists('lead_forwarded_histories');

        // Modify existing lead_histories table
        Schema::create('lead_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('set null');
            $table->string('action')->index(); // created, updated, status_changed, voice_recording_updated, soft_deleted, restored, force_deleted, forwarded
            $table->string('status')->nullable(); // Lead status at the time of action
            $table->foreignId('forwarded_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Recreate lead_forwarded_histories for rollback
        Schema::create('lead_forwarded_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_user_id')->constrained('users')->onDelete('set null');
            $table->foreignId('receiver_user_id')->constrained('users')->onDelete('set null');
            $table->boolean('is_forwarded')->default(true);
            $table->timestamp('forwarded_at')->nullable();
            $table->timestamps();
        });

        // Revert lead_histories to original structure
        Schema::dropIfExists('lead_histories');
        Schema::create('lead_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('set null');
            $table->string('action');
            $table->string('status')->nullable();
            $table->foreignId('forwarded_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }
};
?>