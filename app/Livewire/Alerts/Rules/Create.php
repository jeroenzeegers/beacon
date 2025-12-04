<?php

declare(strict_types=1);

namespace App\Livewire\Alerts\Rules;

use App\Models\AlertChannel;
use App\Models\AlertRule;
use App\Models\Monitor;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public ?int $ruleId = null;

    public string $name = '';

    public string $trigger = 'monitor_down';

    public string $scope = 'global';

    public ?int $monitor_id = null;

    public ?int $project_id = null;

    public int $cooldown_minutes = 5;

    public bool $is_active = true;

    /** @var array<int> */
    public array $channel_ids = [];

    // Conditions
    public ?int $response_time_threshold = null;

    public ?int $ssl_days_threshold = null;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $rule = AlertRule::where('team_id', Auth::user()->current_team_id)
                ->with('channels')
                ->findOrFail($id);

            $this->ruleId = $rule->id;
            $this->name = $rule->name;
            $this->trigger = $rule->trigger;
            $this->cooldown_minutes = $rule->cooldown_minutes;
            $this->is_active = $rule->is_active;
            $this->channel_ids = $rule->channels->pluck('id')->toArray();

            if ($rule->monitor_id) {
                $this->scope = 'monitor';
                $this->monitor_id = $rule->monitor_id;
            } elseif ($rule->project_id) {
                $this->scope = 'project';
                $this->project_id = $rule->project_id;
            } else {
                $this->scope = 'global';
            }

            $conditions = $rule->conditions ?? [];
            $this->response_time_threshold = $conditions['response_time_threshold'] ?? null;
            $this->ssl_days_threshold = $conditions['ssl_days_threshold'] ?? null;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'trigger' => ['required', Rule::in(array_keys(AlertRule::getAvailableTriggers()))],
            'scope' => ['required', Rule::in(['global', 'monitor', 'project'])],
            'monitor_id' => ['nullable', 'required_if:scope,monitor', 'exists:monitors,id'],
            'project_id' => ['nullable', 'required_if:scope,project', 'exists:projects,id'],
            'cooldown_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'is_active' => ['boolean'],
            'channel_ids' => ['required', 'array', 'min:1'],
            'channel_ids.*' => ['exists:alert_channels,id'],
        ]);

        $team = Auth::user()->currentTeam;

        $conditions = [];
        if ($this->response_time_threshold) {
            $conditions['response_time_threshold'] = $this->response_time_threshold;
        }
        if ($this->ssl_days_threshold) {
            $conditions['ssl_days_threshold'] = $this->ssl_days_threshold;
        }

        $data = [
            'team_id' => $team->id,
            'name' => $this->name,
            'trigger' => $this->trigger,
            'monitor_id' => $this->scope === 'monitor' ? $this->monitor_id : null,
            'project_id' => $this->scope === 'project' ? $this->project_id : null,
            'cooldown_minutes' => $this->cooldown_minutes,
            'conditions' => $conditions ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->ruleId) {
            $rule = AlertRule::where('team_id', $team->id)->findOrFail($this->ruleId);
            $rule->update($data);
            $message = 'Alert rule updated successfully.';
        } else {
            $rule = AlertRule::create($data);
            $message = 'Alert rule created successfully.';
        }

        $rule->channels()->sync($this->channel_ids);

        session()->flash('message', $message);
        $this->redirect(route('alerts.rules.index'), navigate: true);
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        return view('livewire.alerts.rules.create', [
            'triggers' => AlertRule::getAvailableTriggers(),
            'monitors' => Monitor::where('team_id', $team->id)->orderBy('name')->get(),
            'projects' => Project::where('team_id', $team->id)->orderBy('name')->get(),
            'channels' => AlertChannel::where('team_id', $team->id)->active()->orderBy('name')->get(),
            'isEditing' => $this->ruleId !== null,
        ])->layout('layouts.app');
    }
}
