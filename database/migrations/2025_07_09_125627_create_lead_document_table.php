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
        Schema::create('lead_document', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('upload_by'); // user ID who uploaded the file
            $table->string('filepath'); // path to uploaded document
            $table->timestamp('uploaded_at')->nullable();

            $table->timestamps(); // created_at, updated_at

            // Foreign Keys (optional but recommended)
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('upload_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_document');
    }
};
