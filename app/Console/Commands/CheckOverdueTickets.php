<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckOverdueTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark tickets past their deadline as Overdue and permanently record the SLA breach';

    /**
     * Execute the console command.
     *
     * When a ticket breaches its SLA deadline:
     *  1. Its status is changed to "overdue" so the UI clearly shows it.
     *  2. sla_breached is permanently set to TRUE — this NEVER resets, even after resolution.
     *
     * The technician can later move the ticket: Overdue → In Progress → Resolved.
     * The employee can then close it. But sla_breached stays TRUE forever.
     */
    public function handle()
    {
        // Only pick tickets that are still active and haven't been breached yet
        $overdueTickets = \App\Models\Ticket::whereNotIn('status', ['resolved', 'closed', 'overdue'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->get();

        foreach ($overdueTickets as $ticket) {
            // 1. Change status to "overdue" so it shows in workflow views
            // 2. Permanently record SLA breach (never reset by future status changes)
            $ticket->update([
                'status'       => 'overdue',
                'sla_breached' => true,
            ]);

            // Log activity for audit trail
            \App\Models\TicketActivity::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => null, // System action
                'action'      => 'overdue',
                'description' => 'SLA deadline breached — ticket marked as Overdue',
            ]);

            // Send notification emails
            try {
                $employeeEmail   = $ticket->user->email;
                $admins          = \App\Models\User::where('role', 'admin')->pluck('email')->toArray();
                $techEmail       = $ticket->technician ? $ticket->technician->email : null;
                $supervisorEmail = $ticket->technician ? $ticket->technician->supervisor_email : null;

                $mail = \Illuminate\Support\Facades\Mail::to($employeeEmail);

                $cc = $admins;
                if ($techEmail) {
                    $cc[] = $techEmail;
                }
                if ($supervisorEmail) {
                    $cc[] = $supervisorEmail;
                }
                $mail->cc($cc)->send(new \App\Mail\TicketOverdue($ticket));
            } catch (\Exception $e) {
                $this->error("Failed to send overdue email for ticket {$ticket->ticket_id}: " . $e->getMessage());
            }

            $this->info("Ticket {$ticket->ticket_id} marked as Overdue. SLA breach permanently recorded.");
        }

        if ($overdueTickets->count() === 0) {
            $this->info('No newly overdue tickets found.');
        }
    }
}
