<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
// displays motivational quotes in the terminal
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
// Sends reminder emails to employees/technicians about upcoming ticket deadlines
// Scans all tickets and marks them as Overdue if their due_at date has passed
Schedule::command('tickets:send-reminders')->everyMinute();
Schedule::command('tickets:check-overdue')->everyMinute();
