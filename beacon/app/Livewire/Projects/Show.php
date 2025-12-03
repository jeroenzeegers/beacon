<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Models\Monitor;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Project $project;

    public function mount(int $id): void
    {
        $this->project = Project::where('team_id', Auth::user()->current_team_id)
            ->withCount('monitors')
            ->findOrFail($id);
    }

    public function delete(): void
    {
        $this->project->delete();

        session()->flash('message', 'Project deleted successfully.');

        $this->redirect(route('projects.index'), navigate: true);
    }

    public function render()
    {
        $monitors = Monitor::where('project_id', $this->project->id)
            ->with('latestCheck')
            ->orderBy('name')
            ->get();

        $stats = [
            'total_monitors' => $monitors->count(),
            'monitors_up' => $monitors->where('status', Monitor::STATUS_UP)->count(),
            'monitors_down' => $monitors->where('status', Monitor::STATUS_DOWN)->count(),
            'monitors_degraded' => $monitors->where('status', Monitor::STATUS_DEGRADED)->count(),
        ];

        return view('livewire.projects.show', [
            'monitors' => $monitors,
            'stats' => $stats,
            'overallStatus' => $this->project->overall_status,
        ])->layout('layouts.app');
    }
}
