<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TicketActivity;

$activities = TicketActivity::where('action', 'assigned')->where('created_at', '>=', '2026-05-15')->get();
foreach ($activities as $a) {
    echo "Time: {$a->created_at}, Desc: {$a->description}\n";
}
