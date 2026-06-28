<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add sla_breached flag to tickets.
     *
     * Once set to true (when the ticket becomes Overdue), this flag is NEVER reset.
     * This allows reporting to correctly count SLA breaches even after the ticket
     * is later resolved or closed.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('sla_breached')->default(false)->after('due_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('sla_breached');
        });
    }
};
