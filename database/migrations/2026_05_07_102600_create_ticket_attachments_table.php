<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the 'ticket_attachments' table to store multiple file uploads per ticket.
     * Supports images (PNG, JPG) and videos (MP4) up to 25MB each.
     */
    public function up(): void
    {
        Schema::create('ticket_attachments', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key linking the attachment to its parent ticket
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');

            // The stored file path on disk (relative to storage/app/public)
            $table->string('file_path');

            // Original filename uploaded by the user
            $table->string('original_name');

            // MIME type of the file (e.g. image/png, video/mp4)
            $table->string('mime_type');

            // File size in bytes
            $table->unsignedBigInteger('file_size');

            // Timestamps for created_at and updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
