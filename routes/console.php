<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Check for delayed contracts every day at 8 AM
Schedule::command('contracts:check-delays')->dailyAt('08:00');

// Check arrival dates — notify 1 day before and alert on overdue
Schedule::command('contracts:check-arrivals')->dailyAt('07:00');

// Notify branch managers about leads not contacted within 24 hours
Schedule::command('leads:notify-stale')->dailyAt('09:00');

// Notify about complaints still open after 7 days
Schedule::command('complaints:notify-stale')->dailyAt('09:30');

// Notify about housing assignments past their expected checkout date
Schedule::command('housing:check-overdue')->dailyAt('08:30');
