<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Incident;
use App\Models\Monitor;
use App\Models\Project;
use App\Services\UsageLimiter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Get the listeners for the component.
     */
    public function getListeners(): array
    {
        $teamId = Auth::user()->current_team_id;

        return [
            "echo-private:team.{$teamId},monitor.status.changed" => 'handleMonitorStatusChange',
            "echo-private:team.{$teamId},monitor.check.completed" => 'handleCheckCompleted',
            "echo-private:team.{$teamId},alert.triggered" => 'handleAlertTriggered',
        ];
    }

    /**
     * Handle monitor status change event.
     */
    public function handleMonitorStatusChange(array $data): void
    {
        // Refresh the component to show updated data
        $this->dispatch('monitor-updated', monitorId: $data['monitor']['id']);
    }

    /**
     * Handle check completed event.
     */
    public function handleCheckCompleted(array $data): void
    {
        // Just refresh, the view will show new data
    }

    /**
     * Handle alert triggered event.
     */
    public function handleAlertTriggered(array $data): void
    {
        $this->dispatch('notify', [
            'type' => 'warning',
            'message' => "Alert: {$data['monitor']['name']} - {$data['alert']['message']}",
        ]);
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        $monitors = Monitor::where('team_id', $team->id)
            ->with('latestCheck')
            ->get();

        $projects = Project::where('team_id', $team->id)
            ->withCount('monitors')
            ->get();

        $activeIncidents = Incident::where('team_id', $team->id)
            ->active()
            ->with('monitor')
            ->orderBy('started_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total_monitors' => $monitors->count(),
            'monitors_up' => $monitors->where('status', Monitor::STATUS_UP)->count(),
            'monitors_down' => $monitors->where('status', Monitor::STATUS_DOWN)->count(),
            'monitors_degraded' => $monitors->where('status', Monitor::STATUS_DEGRADED)->count(),
            'total_projects' => $projects->count(),
            'active_incidents' => $activeIncidents->count(),
        ];

        $usageLimiter = app(UsageLimiter::class);
        $remainingLimits = $usageLimiter->getRemainingLimits($team);

        return view('livewire.dashboard', [
            'monitors' => $monitors,
            'projects' => $projects,
            'activeIncidents' => $activeIncidents,
            'stats' => $stats,
            'remainingLimits' => $remainingLimits,
            'team' => $team,
        ])->layout('layouts.app');
    }
}
