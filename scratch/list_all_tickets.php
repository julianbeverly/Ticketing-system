<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

$tickets = Ticket::all();
foreach ($tickets as $t) {
    echo "DB_ID: {$t->id}, LABEL: {$t->ticket_id}, TechID: {$t->technician_id}, Status: {$t->status}\n";
}