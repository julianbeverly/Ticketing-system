<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // e.g. created, assigned, status_updated
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Backfill existing tickets
        $tickets = DB::table('tickets')->get();
        $activities = [];
        foreach ($tickets as $ticket) {
            $activities[] = [
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->user_id,
                'action' => 'created',
                'description' => 'created a new ticket',
                'created_at' => $ticket->created_at,
                'updated_at' => $ticket->created_at,
            ];
        }

        if (!empty($activities)) {
            DB::table('ticket_activities')->insert($activities);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_activities');
    }
};
