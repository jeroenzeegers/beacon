<?php

declare(strict_types=1);

namespace App\Livewire\Alerts\Logs;

use App\Models\AlertLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    #[Url]
    public string $trigger = '';

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        $logs = AlertLog::where('team_id', $team->id)
            ->with(['alertRule', 'alertChannel', 'monitor'])
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->trigger, function ($query) {
                $query->where('trigger', $this->trigger);
            })
            ->orderBy('sent_at', 'desc')
            ->paginate(25);

        $triggers = [
            'monitor_down' => 'Monitor Down',
            'monitor_up' => 'Monitor Up',
            'monitor_degraded' => 'Monitor Degraded',
            'ssl_expiring' => 'SSL Expiring',
            'response_slow' => 'Response Slow',
            'status_change' => 'Status Change',
        ];

        return view('livewire.alerts.logs.index', [
            'logs' => $logs,
            'triggers' => $triggers,
        ])->layout('layouts.app');
    }
}
