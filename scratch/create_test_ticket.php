<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;
use App\Models\User;

$tech = User::where('role', 'technician')->first();
$user = User::where('role', 'employee')->first();

if (!$tech || !$user) {
    die("Need a tech and a user to create a ticket.\n");
}

$ticket = Ticket::create([
    'ticket_id' => 'TEST-' . rand(1000, 9999),
    'user_id' => $user->id,
    'subject' => 'Test Reminder Ticket',
    'description' => 'Testing the reminder logic',
    'priority' => 'high',
    'status' => 'assigned',
    'technician_id' => $tech->id,
    'reminder_interval' => '30mins',
    'due_at' => now()->addHours(2),
    'last_reminder_at' => now()->subMinutes(35), // Force it to be due for a reminder
]);

echo "Created Ticket: {$ticket->ticket_id}\n";
echo "Status: {$ticket->status}\n";
echo "Reminder Interval: {$ticket->reminder_interval}\n";
echo "Last Reminder: {$ticket->last_reminder_at}\n";
