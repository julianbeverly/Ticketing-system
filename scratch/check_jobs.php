<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = Illuminate\Support\Facades\DB::table('jobs')->count();
echo "Pending jobs: " . $count . "\n";
