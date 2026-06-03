<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

$tickets = Ticket::with('technician')->get();
foreach ($tickets as $t) {
    echo "ID: {$t->ticket_id}, Tech: " . ($t->technician->name ?? 'None') . ", Status: {$t->status}\n";
}
