<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Team;
use App\Notifications\TrialExpiredNotification;
use App\Notifications\TrialExpiringNotification;
use Illuminate\Console\Command;

class CheckExpiringTrials extends Command
{
    protected $signature = 'trials:check';

    protected $description = 'Check for expiring and expired trials and send notifications';

    public function handle(): int
    {
        $this->info('Checking trial statuses...');

        $this->checkExpiringTrials();
        $this->checkExpiredTrials();

        $this->info('Done!');

        return self::SUCCESS;
    }

    private function checkExpiringTrials(): void
    {
        $this->line('Checking for trials expiring in 3 days...');

        // Find teams with trials expiring in 3 days (within a 24-hour window)
        $teams = Team::query()
            ->whereHas('subscriptions', function ($query) {
                $query->where('trial_ends_at', '>=', now()->addDays(2))
                    ->where('trial_ends_at', '<=', now()->addDays(4))
                    ->whereNull('ends_at');
            })
            ->with('owner')
            ->get();

        $count = 0;

        foreach ($teams as $team) {
            $subscription = $team->subscription('default');

            if (! $subscription || ! $subscription->onTrial()) {
                continue;
            }

            // Check if we've already notified (using meta or a separate table in production)
            $notificationKey = "trial_expiring_notified_{$team->id}";

            if (cache()->has($notificationKey)) {
                continue;
            }

            // Send notification to team owner
            try {
                $team->owner->notify(new TrialExpiringNotification($team, $subscription->trial_ends_at));
                cache()->put($notificationKey, true, now()->addDays(7));
                $count++;

                $this->line("  Notified {$team->owner->email} about expiring trial for {$team->name}");
            } catch (\Exception $e) {
                $this->error("  Failed to notify {$team->owner->email}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} expiring trial notifications.");
    }

    private function checkExpiredTrials(): void
    {
        $this->line('Checking for expired trials...');

        // Find teams with expired trials (expired within last 24 hours)
        $teams = Team::query()
            ->whereHas('subscriptions', function ($query) {
                $query->where('trial_ends_at', '>=', now()->subDay())
                    ->where('trial_ends_at', '<=', now())
                    ->whereNull('ends_at')
                    ->where('stripe_status', '!=', 'active');
            })
            ->with('owner')
            ->get();

        $count = 0;

        foreach ($teams as $team) {
            $subscription = $team->subscription('default');

            if (! $subscription) {
                continue;
            }

            // Check if we've already notified
            $notificationKey = "trial_expired_notified_{$team->id}";

            if (cache()->has($notificationKey)) {
                continue;
            }

            // Send notification to team owner
            try {
                $team->owner->notify(new TrialExpiredNotification($team));
                cache()->put($notificationKey, true, now()->addDays(30));
                $count++;

                $this->line("  Notified {$team->owner->email} about expired trial for {$team->name}");
            } catch (\Exception $e) {
                $this->error("  Failed to notify {$team->owner->email}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} expired trial notifications.");
    }
}
