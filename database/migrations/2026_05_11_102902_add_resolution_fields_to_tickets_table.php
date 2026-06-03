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
        Schema::table('tickets', function (Blueprint $table) {
            $table->text('resolution_note')->nullable()->after('last_reminder_at');
            $table->string('resolution_type')->nullable()->after('resolution_note');
            $table->timestamp('resolved_at')->nullable()->after('resolution_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['resolution_note', 'resolution_type', 'resolved_at']);
        });
    }
};
