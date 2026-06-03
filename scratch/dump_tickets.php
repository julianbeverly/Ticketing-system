<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

$tickets = Ticket::all(['id', 'ticket_id', 'technician_id', 'status', 'resolved_at']);
echo json_encode($tickets, JSON_PRETTY_PRINT);
