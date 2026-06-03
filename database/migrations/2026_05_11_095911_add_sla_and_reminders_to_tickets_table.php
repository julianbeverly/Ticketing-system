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
            $table->timestamp('due_at')->nullable()->after('status');
            $table->string('reminder_interval')->nullable()->after('due_at');
            $table->timestamp('last_reminder_at')->nullable()->after('reminder_interval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['due_at', 'reminder_interval', 'last_reminder_at']);
        });
    }
};
