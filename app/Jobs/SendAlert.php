<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AlertLog;
use App\Models\AlertRule;
use App\Models\Monitor;
use App\Support\AlertSenders\AlertSenderFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendAlert implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Monitor $monitor,
        public string $previousStatus,
        public string $newStatus
    ) {
        $this->onQueue('alerts');
    }

    public function handle(AlertSenderFactory $senderFactory): void
    {
        $trigger = $this->determineTrigger();
        $message = $this->buildMessage();

        // Find applicable alert rules
        $rules = AlertRule::query()
            ->where('team_id', $this->monitor->team_id)
            ->active()
            ->forMonitor($this->monitor->id)
            ->with('channels')
            ->get();

        if ($rules->isEmpty()) {
            Log::debug('No alert rules found for monitor', [
                'monitor_id' => $this->monitor->id,
                'trigger' => $trigger,
            ]);

            return;
        }

        foreach ($rules as $rule) {
            if (! $rule->shouldTrigger($trigger, $this->monitor)) {
                Log::debug('Rule skipped (conditions not met)', [
                    'rule_id' => $rule->id,
                    'monitor_id' => $this->monitor->id,
                    'trigger' => $trigger,
                ]);

                continue;
            }

            $this->processRule($rule, $trigger, $message, $senderFactory);
        }
    }

    private function processRule(AlertRule $rule, string $trigger, string $message, AlertSenderFactory $senderFactory): void
    {
        foreach ($rule->channels as $channel) {
            if (! $channel->is_active) {
                continue;
            }

            try {
                if (! $senderFactory->hasSender($channel->type)) {
                    Log::warning('No sender available for channel type', [
                        'channel_type' => $channel->type,
                        'channel_id' => $channel->id,
                    ]);

                    continue;
                }

                $sender = $senderFactory->getSender($channel->type);
                $success = $sender->send($channel, $this->monitor, $trigger, $message);

                AlertLog::log(
                    teamId: $this->monitor->team_id,
                    trigger: $trigger,
                    status: $success ? AlertLog::STATUS_SENT : AlertLog::STATUS_FAILED,
                    message: $message,
                    alertRuleId: $rule->id,
                    alertChannelId: $channel->id,
                    monitorId: $this->monitor->id,
                    metadata: [
                        'previous_status' => $this->previousStatus,
                        'new_status' => $this->newStatus,
                    ],
                    errorMessage: $success ? null : 'Failed to send alert',
                );

                Log::info('Alert sent', [
                    'monitor_id' => $this->monitor->id,
                    'channel_id' => $channel->id,
                    'channel_type' => $channel->type,
                    'success' => $success,
                ]);
            } catch (\Exception $e) {
                AlertLog::log(
                    teamId: $this->monitor->team_id,
                    trigger: $trigger,
                    status: AlertLog::STATUS_FAILED,
                    message: $message,
                    alertRuleId: $rule->id,
                    alertChannelId: $channel->id,
                    monitorId: $this->monitor->id,
                    errorMessage: $e->getMessage(),
                );

                Log::error('Failed to send alert', [
                    'monitor_id' => $this->monitor->id,
                    'channel_id' => $channel->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function determineTrigger(): string
    {
        return match ($this->newStatus) {
            Monitor::STATUS_DOWN => AlertRule::TRIGGER_MONITOR_DOWN,
            Monitor::STATUS_UP => AlertRule::TRIGGER_MONITOR_UP,
            Monitor::STATUS_DEGRADED => AlertRule::TRIGGER_MONITOR_DEGRADED,
            default => AlertRule::TRIGGER_STATUS_CHANGE,
        };
    }

    private function buildMessage(): string
    {
        $statusText = match ($this->newStatus) {
            Monitor::STATUS_DOWN => 'is DOWN',
            Monitor::STATUS_UP => 'is UP (recovered)',
            Monitor::STATUS_DEGRADED => 'is DEGRADED',
            default => "changed status to {$this->newStatus}",
        };

        $message = "Monitor **{$this->monitor->name}** {$statusText}.\n\n";
        $message .= "**Target:** {$this->monitor->target}\n";
        $message .= '**Type:** '.ucfirst($this->monitor->type)."\n";
        $message .= '**Previous Status:** '.ucfirst($this->previousStatus)."\n";
        $message .= '**Time:** '.now()->format('Y-m-d H:i:s T');

        // Add latest check info if available
        $latestCheck = $this->monitor->latestCheck;
        if ($latestCheck) {
            if ($latestCheck->response_time) {
                $message .= "\n**Response Time:** {$latestCheck->response_time}ms";
            }
            if ($latestCheck->error_message) {
                $message .= "\n**Error:** {$latestCheck->error_message}";
            }
        }

        return $message;
    }
}
