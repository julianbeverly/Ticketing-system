<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

$tickets = Ticket::all(['id', 'ticket_id', 'status', 'due_at', 'reminder_interval', 'technician_id', 'last_reminder_at']);
echo $tickets->toJson(JSON_PRETTY_PRINT);
