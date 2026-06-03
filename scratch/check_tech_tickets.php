<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Ticket;

$techs = User::where('role', 'technician')->get();
foreach ($techs as $tech) {
    echo "Tech: {$tech->name} (ID: {$tech->id})\n";
    $tickets = Ticket::where('technician_id', $tech->id)->get();
    foreach ($tickets as $t) {
        echo "  - Ticket: {$t->ticket_id}, Status: {$t->status}, Created: {$t->created_at}, Resolved: {$t->resolved_at}\n";
    }
}
