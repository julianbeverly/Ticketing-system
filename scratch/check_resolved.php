<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

$tickets = Ticket::whereNotNull('resolved_at')->get();
foreach ($tickets as $t) {
    echo "ID: {$t->ticket_id}, TechID: {$t->technician_id}, Resolved: {$t->resolved_at}\n";
}
