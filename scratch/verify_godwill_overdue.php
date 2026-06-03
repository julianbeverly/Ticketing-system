<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Ticket;
use Carbon\Carbon;

$tech = User::where('name', 'Godwill')->first();

echo "Creating OVERDUE test ticket for Godwill...\n";
$ticket = Ticket::create([
    'ticket_id' => 'TEST-GODWILL-OVERDUE',
    'user_id' => 10,
    'technician_id' => $tech->id,
    'subject' => 'Overdue Ticket',
    'description' => 'This should lower the score',
    'priority' => 'high',
    'status' => 'resolved',
    'created_at' => now()->subDays(2),
    'due_at' => now()->subDays(1),
    'resolved_at' => now(), // Resolved after due_at
]);

$controller = new \App\Http\Controllers\DashboardController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('calculateSlaData');
$method->setAccessible(true);
$data = $method->invoke($controller, now()->month, now()->year);

foreach ($data as $item) {
    if ($item['name'] === 'Godwill') {
        echo "Godwill Stats (with 1 good, 1 overdue):\n";
        echo "  Assigned: {$item['assigned']}\n";
        echo "  Resolved: {$item['resolved']}\n";
        echo "  SLA Score: {$item['sla_score']}%\n";
    }
}
