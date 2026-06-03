<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TicketActivity;

$activities = TicketActivity::where('description', 'like', '%Godwill%')->get();
foreach ($activities as $a) {
    echo "Time: {$a->created_at}, Desc: {$a->description}\n";
}
