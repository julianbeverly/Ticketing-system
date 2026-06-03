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
    protected $description = 'Check for tickets past their deadline and mark them as overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overdueTickets = \App\Models\Ticket::whereNotIn('status', ['resolved', 'closed', 'overdue'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->get();

        foreach ($overdueTickets as $ticket) {
            $ticket->update(['status' => 'overdue']);

            \App\Models\TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => null, // System action
                'action' => 'overdue',
                'description' => 'marked as overdue',
            ]);

            try {
                $employeeEmail = $ticket->user->email;
                $admins = \App\Models\User::where('role', 'admin')->pluck('email')->toArray();
                $techEmail = $ticket->technician ? $ticket->technician->email : null;
// If technician exists get supervisor email from technician->supervisor_email, otherwise set to null
                $supervisorEmail = $ticket->technician ? $ticket->technician->supervisor_email  : null;

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

            $this->info("Ticket {$ticket->ticket_id} marked as overdue.");
        }
    }
}
