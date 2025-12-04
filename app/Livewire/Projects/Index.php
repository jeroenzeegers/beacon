<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Services\UsageLimiter;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $environment = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $project = Project::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $project->delete();

        session()->flash('message', 'Project deleted successfully.');
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        $projects = Project::where('team_id', $team->id)
            ->withCount('monitors')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->environment, function ($query) {
                $query->where('environment', $this->environment);
            })
            ->orderBy('name')
            ->paginate(15);

        $usageLimiter = app(UsageLimiter::class);
        $canCreateProject = $usageLimiter->canCreateProject($team);

        return view('livewire.projects.index', [
            'projects' => $projects,
            'canCreateProject' => $canCreateProject,
            'environments' => ['production', 'staging', 'development'],
        ])->layout('layouts.app');
    }
}
