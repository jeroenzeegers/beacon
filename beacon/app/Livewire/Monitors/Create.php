<?php

declare(strict_types=1);

namespace App\Livewire\Monitors;

use App\Models\Monitor;
use App\Models\Project;
use App\Services\UsageLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public ?int $monitorId = null;

    public string $name = '';
    public string $type = 'http';
    public string $target = '';
    public ?int $port = null;
    public ?int $project_id = null;
    public int $check_interval = 300;
    public int $timeout = 30;
    public int $failure_threshold = 3;

    // HTTP options
    public string $http_method = 'GET';
    public array $http_headers = [];
    public string $http_body = '';
    public string $expected_status = '200,201,204,301,302';
    public string $expected_body = '';

    // SSL options
    public int $ssl_warning_days = 30;
    public int $ssl_critical_days = 7;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $monitor = Monitor::where('team_id', Auth::user()->current_team_id)
                ->findOrFail($id);

            $this->monitorId = $monitor->id;
            $this->name = $monitor->name;
            $this->type = $monitor->type;
            $this->target = $monitor->target;
            $this->port = $monitor->port;
            $this->project_id = $monitor->project_id;
            $this->check_interval = $monitor->check_interval;
            $this->timeout = $monitor->timeout;
            $this->failure_threshold = $monitor->failure_threshold;

            if ($monitor->http_options) {
                $this->http_method = $monitor->http_options['method'] ?? 'GET';
                $this->http_headers = $monitor->http_options['headers'] ?? [];
                $this->http_body = $monitor->http_options['body'] ?? '';
                $this->expected_status = implode(',', $monitor->http_options['expected_status'] ?? [200]);
                $this->expected_body = $monitor->http_options['expected_body'] ?? '';
            }

            if ($monitor->ssl_options) {
                $this->ssl_warning_days = $monitor->ssl_options['warning_days'] ?? 30;
                $this->ssl_critical_days = $monitor->ssl_options['critical_days'] ?? 7;
            }
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Monitor::getAvailableTypes()))],
            'target' => ['required', 'string', 'max:500'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'check_interval' => ['required', 'integer', 'min:30'],
            'timeout' => ['required', 'integer', 'min:5', 'max:120'],
            'failure_threshold' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $team = Auth::user()->currentTeam;

        // Check usage limits for new monitors
        if (!$this->monitorId) {
            $usageLimiter = app(UsageLimiter::class);
            if (!$usageLimiter->canCreateMonitor($team)) {
                session()->flash('error', 'You have reached your monitor limit. Please upgrade your plan.');
                return;
            }

            // Check interval limit
            if (!$usageLimiter->canUseCheckInterval($team, $this->check_interval)) {
                $minInterval = $usageLimiter->getMinCheckInterval($team);
                session()->flash('error', "Your plan requires a minimum check interval of {$minInterval} seconds.");
                return;
            }
        }

        $httpOptions = null;
        if (in_array($this->type, [Monitor::TYPE_HTTP, Monitor::TYPE_HTTPS])) {
            $httpOptions = [
                'method' => $this->http_method,
                'headers' => $this->http_headers,
                'body' => $this->http_body ?: null,
                'expected_status' => array_map('intval', explode(',', $this->expected_status)),
                'expected_body' => $this->expected_body ?: null,
            ];
        }

        $sslOptions = null;
        if ($this->type === Monitor::TYPE_SSL_EXPIRY) {
            $sslOptions = [
                'warning_days' => $this->ssl_warning_days,
                'critical_days' => $this->ssl_critical_days,
            ];
        }

        $data = [
            'team_id' => $team->id,
            'name' => $this->name,
            'type' => $this->type,
            'target' => $this->target,
            'port' => $this->port,
            'project_id' => $this->project_id,
            'check_interval' => $this->check_interval,
            'timeout' => $this->timeout,
            'failure_threshold' => $this->failure_threshold,
            'http_options' => $httpOptions,
            'ssl_options' => $sslOptions,
        ];

        if ($this->monitorId) {
            $monitor = Monitor::where('team_id', $team->id)->findOrFail($this->monitorId);
            $monitor->update($data);
            session()->flash('message', 'Monitor updated successfully.');
        } else {
            Monitor::create($data);
            session()->flash('message', 'Monitor created successfully.');
        }

        $this->redirect(route('monitors.index'), navigate: true);
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;
        $projects = Project::where('team_id', $team->id)->get();

        $usageLimiter = app(UsageLimiter::class);
        $minCheckInterval = $usageLimiter->getMinCheckInterval($team);

        return view('livewire.monitors.create', [
            'projects' => $projects,
            'monitorTypes' => Monitor::getAvailableTypes(),
            'minCheckInterval' => $minCheckInterval,
            'isEditing' => (bool) $this->monitorId,
        ])->layout('layouts.app');
    }
}
