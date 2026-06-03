<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Ticket;
use Carbon\Carbon;

$month = 5;
$year = 2026;

$technicians = User::where('role', 'technician')->get();
foreach ($technicians as $tech) {
    $tickets = Ticket::where('technician_id', $tech->id)
        ->where(function($q) use ($month, $year) {
            $q->where(function($sq) use ($month, $year) {
                $sq->whereMonth('created_at', $month)
                   ->whereYear('created_at', $year);
            })->orWhere(function($sq) use ($month, $year) {
                $sq->whereMonth('resolved_at', $month)
                   ->whereYear('resolved_at', $year);
            });
        })->get();
    
    echo "Tech: {$tech->name} (ID: {$tech->id})\n";
    echo "  Tickets found: " . $tickets->count() . "\n";
    foreach ($tickets as $t) {
        echo "    - {$t->ticket_id}, Created: {$t->created_at}, Resolved: {$t->resolved_at}, Status: {$t->status}\n";
    }
}
