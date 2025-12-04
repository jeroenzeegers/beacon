<?php

use App\Jobs\CheckHeartbeats;
use App\Jobs\SendScheduledReport;
use App\Jobs\UpdateMaintenanceWindowStatus;
use App\Models\ScheduledReport;
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

// Check heartbeats every minute
Schedule::job(new CheckHeartbeats)
    ->everyMinute()
    ->withoutOverlapping();

// Update maintenance window statuses every minute
Schedule::job(new UpdateMaintenanceWindowStatus)
    ->everyMinute()
    ->withoutOverlapping();

// Process scheduled reports every minute
Schedule::call(function () {
    $reports = ScheduledReport::where('is_active', true)
        ->where('next_send_at', '<=', now())
        ->get();

    foreach ($reports as $report) {
        SendScheduledReport::dispatch($report);
    }
})->everyMinute()->name('process-scheduled-reports')->withoutOverlapping();

// Clean up old data daily at 2 AM
Schedule::command('data:cleanup')
    ->dailyAt('02:00')
    ->withoutOverlapping();

// Check expiring trials daily at 9 AM
Schedule::command('trials:check')
    ->dailyAt('09:00')
    ->withoutOverlapping();
