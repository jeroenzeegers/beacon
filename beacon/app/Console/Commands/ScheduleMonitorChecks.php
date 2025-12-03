<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\PerformMonitorCheck;
use App\Models\Monitor;
use Illuminate\Console\Command;

class ScheduleMonitorChecks extends Command
{
    protected $signature = 'monitors:schedule
                            {--limit=500 : Maximum number of monitors to schedule per run}';

    protected $description = 'Schedule checks for monitors that are due';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $this->info('Scheduling monitor checks...');

        $monitors = Monitor::query()
            ->dueForCheck()
            ->limit($limit)
            ->get();

        if ($monitors->isEmpty()) {
            $this->info('No monitors due for checking.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($monitors as $monitor) {
            PerformMonitorCheck::dispatch($monitor);
            $count++;
        }

        $this->info("Scheduled {$count} monitor checks.");

        return Command::SUCCESS;
    }
}
