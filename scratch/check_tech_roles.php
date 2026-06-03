<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

$tickets = Ticket::whereNotNull('technician_id')->with('technician')->get();
foreach ($tickets as $t) {
    echo "ID: {$t->ticket_id}, TechName: " . ($t->technician->name ?? 'None') . ", TechRole: " . ($t->technician->role ?? 'None') . "\n";
}
