<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Mail\TicketReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTicketReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send periodic reminders to technicians for tickets approaching deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tickets = Ticket::whereIn('status', ['assigned', 'in_progress'])
            ->whereNotNull('due_at')
            ->whereNotNull('reminder_interval')
            ->whereNotNull('technician_id')
            ->get();

        $count = $tickets->count();
        $this->info(now()->format('Y-m-d H:i:s') . ": Checking {$count} active tickets for periodic reminders...");

        if ($count === 0) {
            $this->comment("No eligible tickets found (Status must be 'assigned' or 'in_progress' with a reminder interval).");
            return;
        }

        foreach ($tickets as $ticket) {
            if (!$ticket->technician) {
                $this->warn("Ticket {$ticket->ticket_id} has no technician assigned. Skipping.");
                continue;
            }

            if ($this->shouldSendReminder($ticket)) {
                try {
                    $this->info("Attempting to send reminder for Ticket {$ticket->ticket_id} to {$ticket->technician->email}...");
                    
                    Mail::to($ticket->technician->email)->send(new TicketReminder($ticket));
                    
                    $ticket->update(['last_reminder_at' => now()]);
                    $this->info("SUCCESS: Reminder queued for Ticket {$ticket->ticket_id}.");
                } catch (\Exception $e) {
                    $this->error("FAILED: Could not process reminder for Ticket {$ticket->ticket_id}. Error: " . $e->getMessage());
                    \Log::error("Reminder failed for Ticket {$ticket->ticket_id}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Determine if a reminder should be sent based on the interval and last sent time.
     */
    private function shouldSendReminder($ticket)
    {
        $interval = $ticket->reminder_interval;
        if (empty($interval)) return false;

        $minutes = match($interval) {
            '30mins' => 30,
            '1hour' => 60,
            '24hours' => 1440,
            '1day' => 1440,
            default => null,
        };

        if (!$minutes) return false;

        // Use last_reminder_at, or fallback to the time the ticket was first assigned
        $startTime = $ticket->last_reminder_at ?? $ticket->updated_at;
        
        // Ensure $startTime is a Carbon instance
        if (!$startTime instanceof Carbon) {
            $startTime = Carbon::parse($startTime);
        }

        $diff = (int) abs(now()->diffInMinutes($startTime));
        $this->comment("Ticket {$ticket->ticket_id}: Last reminder {$diff} mins ago. Interval: {$minutes} mins.");

        return $diff >= $minutes;
    }
}
