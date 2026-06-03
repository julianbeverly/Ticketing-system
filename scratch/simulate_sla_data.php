<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\DashboardController;

$controller = new DashboardController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('calculateSlaData');
$method->setAccessible(true);
$data = $method->invoke($controller, 5, 2026);

echo json_encode($data, JSON_PRETTY_PRINT);
