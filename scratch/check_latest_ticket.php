<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

$latest = Ticket::orderBy('id', 'desc')->first();
echo "Latest Ticket ID: " . ($latest->id ?? 'None') . "\n";
echo "Latest Ticket Label: " . ($latest->ticket_id ?? 'None') . "\n";