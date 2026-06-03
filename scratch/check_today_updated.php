<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

$tickets = Ticket::where('updated_at', '>=', '2026-05-15')->get();
foreach ($tickets as $t) {
    echo "ID: {$t->ticket_id}, Status: {$t->status}, Tech: " . ($t->technician->name ?? 'None') . ", Updated: {$t->updated_at}\n";
}
