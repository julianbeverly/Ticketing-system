<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;

Ticket::where('ticket_id', 'like', 'TEST-GODWILL%')->delete();
echo "Test tickets deleted.\n";
