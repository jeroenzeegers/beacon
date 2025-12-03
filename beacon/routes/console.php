<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule monitor checks every 30 seconds
Schedule::command('monitors:schedule')
    ->everyThirtySeconds()
    ->withoutOverlapping()
    ->runInBackground();

// Clean up old data daily at 2 AM
Schedule::command('data:cleanup')
    ->dailyAt('02:00')
    ->withoutOverlapping();

// Check expiring trials daily at 9 AM
Schedule::command('trials:check')
    ->dailyAt('09:00')
    ->withoutOverlapping();
