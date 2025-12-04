<?php

declare(strict_types=1);

namespace App\Livewire\Alerts\Channels;

use App\Models\AlertChannel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public ?int $channelId = null;

    public string $name = '';

    public string $type = 'email';

    public bool $is_active = true;

    public bool $is_default = false;

    // Email config
    public string $config_email = '';

    // Slack/Discord config
    public string $config_webhook_url = '';

    // Webhook config
    public string $config_url = '';

    public string $config_method = 'POST';

    // SMS config
    public string $config_phone_number = '';

    // PagerDuty config
    public string $config_routing_key = '';

    // Telegram config
    public string $config_bot_token = '';

    public string $config_chat_id = '';

    public function mount(?int $id = null): void
    {
        if ($id) {
            $channel = AlertChannel::where('team_id', Auth::user()->current_team_id)
                ->findOrFail($id);

            $this->channelId = $channel->id;
            $this->name = $channel->name;
            $this->type = $channel->type;
            $this->is_active = $channel->is_active;
            $this->is_default = $channel->is_default;

            // Load config based on type
            $config = $channel->config ?? [];
            $this->config_email = $config['email'] ?? '';
            $this->config_webhook_url = $config['webhook_url'] ?? '';
            $this->config_url = $config['url'] ?? '';
            $this->config_method = $config['method'] ?? 'POST';
            $this->config_phone_number = $config['phone_number'] ?? '';
            $this->config_routing_key = $config['routing_key'] ?? '';
            $this->config_bot_token = $config['bot_token'] ?? '';
            $this->config_chat_id = $config['chat_id'] ?? '';
        }
    }

    public function updatedType(): void
    {
        // Reset config fields when type changes
        $this->config_email = '';
        $this->config_webhook_url = '';
        $this->config_url = '';
        $this->config_method = 'POST';
        $this->config_phone_number = '';
        $this->config_routing_key = '';
        $this->config_bot_token = '';
        $this->config_chat_id = '';
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(AlertChannel::getAvailableTypes()))],
            'is_active' => ['boolean'],
            'is_default' => ['boolean'],
        ];

        // Add type-specific validation
        $configRules = match ($this->type) {
            'email' => ['config_email' => ['required', 'email']],
            'slack', 'discord' => ['config_webhook_url' => ['required', 'url']],
            'webhook' => [
                'config_url' => ['required', 'url'],
                'config_method' => ['required', Rule::in(['GET', 'POST', 'PUT', 'PATCH'])],
            ],
            'sms' => ['config_phone_number' => ['required', 'string']],
            'pagerduty' => ['config_routing_key' => ['required', 'string']],
            'telegram' => [
                'config_bot_token' => ['required', 'string'],
                'config_chat_id' => ['required', 'string'],
            ],
            default => [],
        };

        $this->validate(array_merge($rules, $configRules));

        $config = match ($this->type) {
            'email' => ['email' => $this->config_email],
            'slack', 'discord' => ['webhook_url' => $this->config_webhook_url],
            'webhook' => ['url' => $this->config_url, 'method' => $this->config_method],
            'sms' => ['phone_number' => $this->config_phone_number],
            'pagerduty' => ['routing_key' => $this->config_routing_key],
            'telegram' => ['bot_token' => $this->config_bot_token, 'chat_id' => $this->config_chat_id],
            default => [],
        };

        $team = Auth::user()->currentTeam;

        if ($this->is_default) {
            AlertChannel::where('team_id', $team->id)->update(['is_default' => false]);
        }

        if ($this->channelId) {
            $channel = AlertChannel::where('team_id', $team->id)->findOrFail($this->channelId);
            $channel->update([
                'name' => $this->name,
                'type' => $this->type,
                'config' => $config,
                'is_active' => $this->is_active,
                'is_default' => $this->is_default,
            ]);
            $message = 'Alert channel updated successfully.';
        } else {
            AlertChannel::create([
                'team_id' => $team->id,
                'name' => $this->name,
                'type' => $this->type,
                'config' => $config,
                'is_active' => $this->is_active,
                'is_default' => $this->is_default,
            ]);
            $message = 'Alert channel created successfully.';
        }

        session()->flash('message', $message);
        $this->redirect(route('alerts.channels.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.alerts.channels.create', [
            'types' => AlertChannel::getAvailableTypes(),
            'isEditing' => $this->channelId !== null,
        ])->layout('layouts.app');
    }
}
