<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\MaintenanceWindow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UpdateMaintenanceWindowStatus implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Activate scheduled maintenance windows that should start
        $toActivate = MaintenanceWindow::where('status', MaintenanceWindow::STATUS_SCHEDULED)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->get();

        foreach ($toActivate as $window) {
            $window->update(['status' => MaintenanceWindow::STATUS_ACTIVE]);
            Log::info('Maintenance window activated', [
                'id' => $window->id,
                'name' => $window->name,
            ]);
        }

        // Complete maintenance windows that have ended
        $toComplete = MaintenanceWindow::whereIn('status', [
            MaintenanceWindow::STATUS_SCHEDULED,
            MaintenanceWindow::STATUS_ACTIVE,
        ])
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($toComplete as $window) {
            $window->update(['status' => MaintenanceWindow::STATUS_COMPLETED]);
            Log::info('Maintenance window completed', [
                'id' => $window->id,
                'name' => $window->name,
            ]);
        }
    }
}
