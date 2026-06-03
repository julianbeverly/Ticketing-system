<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the 'tickets' table to store support tickets submitted by employees.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Auto-generated display ID shown in the UI (e.g. TC-1001)
            $table->string('ticket_id')->unique();

            // Foreign key to the employee who created the ticket
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Foreign key to the incident category (nullable if custom type is used)
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');

            // Foreign key to the incident type (nullable if custom type is used)
            $table->foreignId('type_id')->nullable()->constrained('types')->onDelete('set null');

            // Free-text incident type when the predefined list doesn't apply
            $table->string('custom_type')->nullable();

            // Short summary of the issue
            $table->string('subject');

            // Detailed description of the problem
            $table->text('description');

            // Priority level: low, medium, high
            $table->string('priority')->default('low');

            // Current ticket status: open, assigned, in_progress, resolved, closed
            $table->string('status')->default('open');

            // Timestamps for created_at and updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
