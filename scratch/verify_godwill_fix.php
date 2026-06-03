<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketActivity;
use Carbon\Carbon;

$tech = User::where('name', 'Godwill')->first();
if (!$tech) {
    echo "Tech Godwill not found.\n";
    exit;
}

echo "Creating test ticket for Godwill...\n";
$ticket = Ticket::create([
    'ticket_id' => 'TEST-GODWILL',
    'user_id' => 10, // mbohmakazi
    'technician_id' => $tech->id,
    'subject' => 'Test Ticket for Godwill',
    'description' => 'Testing graph updates',
    'priority' => 'medium',
    'status' => 'resolved',
    'created_at' => now()->subHours(2),
    'resolved_at' => now(),
    'due_at' => now()->addHours(24),
]);

TicketActivity::create([
    'ticket_id' => $ticket->id,
    'user_id' => $tech->id,
    'action' => 'resolved',
    'description' => 'marked the ticket as resolved',
]);

echo "Ticket created and resolved. Running SLA data simulation...\n";

$controller = new \App\Http\Controllers\DashboardController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('calculateSlaData');
$method->setAccessible(true);
$data = $method->invoke($controller, now()->month, now()->year);

foreach ($data as $item) {
    if ($item['name'] === 'Godwill') {
        echo "Godwill Stats:\n";
        echo "  Assigned: {$item['assigned']}\n";
        echo "  Resolved: {$item['resolved']}\n";
        echo "  SLA Score: {$item['sla_score']}%\n";
    }
}
