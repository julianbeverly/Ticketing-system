<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TicketActivity;

$activities = TicketActivity::orderBy('created_at', 'desc')->take(20)->get();
foreach ($activities as $a) {
    echo "Time: {$a->created_at}, UserID: {$a->user_id}, Action: {$a->action}, Desc: {$a->description}\n";
}
