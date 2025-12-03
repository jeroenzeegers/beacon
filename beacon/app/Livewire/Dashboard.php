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
