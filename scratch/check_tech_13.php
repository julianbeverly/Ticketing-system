<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

$count = Ticket::where('technician_id', 13)->count();
$tickets = Ticket::where('technician_id', 13)->get();

echo "Count for Tech 13: {$count}\n";
foreach($tickets as $t) {
    echo "Ticket: {$t->ticket_id}, Status: {$t->status}\n";
}
