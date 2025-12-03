<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AlertLog;
use App\Models\MonitorCheck;
use App\Models\Team;
use App\Services\UsageLimiter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldData extends Command
{
    protected $signature = 'data:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Clean up old monitor checks and alert logs based on plan retention limits';

    public function __construct(
        private readonly UsageLimiter $usageLimiter
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN - No data will be deleted.');
        }

        $this->info('Starting data cleanup...');

        $totalChecksDeleted = 0;
        $totalLogsDeleted = 0;

        Team::query()->chunk(100, function ($teams) use ($isDryRun, &$totalChecksDeleted, &$totalLogsDeleted) {
            foreach ($teams as $team) {
                $retentionDays = $this->usageLimiter->getPlanLimit($team, 'data_retention_days') ?? 30;
                $cutoffDate = now()->subDays($retentionDays);

                $this->line("Processing team {$team->id} ({$team->name}) - Retention: {$retentionDays} days");

                // Get monitor IDs for this team
                $monitorIds = $team->monitors()->pluck('id');

                if ($monitorIds->isNotEmpty()) {
                    // Count checks to delete
                    $checksToDelete = MonitorCheck::whereIn('monitor_id', $monitorIds)
                        ->where('checked_at', '<', $cutoffDate)
                        ->count();

                    if ($checksToDelete > 0) {
                        $this->line("  - Found {$checksToDelete} old monitor checks");

                        if (!$isDryRun) {
                            MonitorCheck::whereIn('monitor_id', $monitorIds)
                                ->where('checked_at', '<', $cutoffDate)
                                ->delete();
                        }

                        $totalChecksDeleted += $checksToDelete;
                    }
                }

                // Clean up old alert logs
                $logsToDelete = AlertLog::where('team_id', $team->id)
                    ->where('created_at', '<', $cutoffDate)
                    ->count();

                if ($logsToDelete > 0) {
                    $this->line("  - Found {$logsToDelete} old alert logs");

                    if (!$isDryRun) {
                        AlertLog::where('team_id', $team->id)
                            ->where('created_at', '<', $cutoffDate)
                            ->delete();
                    }

                    $totalLogsDeleted += $logsToDelete;
                }
            }
        });

        $this->newLine();
        $this->info('Cleanup complete!');
        $this->table(
            ['Type', 'Count'],
            [
                ['Monitor Checks Deleted', $totalChecksDeleted],
                ['Alert Logs Deleted', $totalLogsDeleted],
            ]
        );

        if ($isDryRun) {
            $this->warn('This was a dry run. Run without --dry-run to actually delete data.');
        }

        return self::SUCCESS;
    }
}
