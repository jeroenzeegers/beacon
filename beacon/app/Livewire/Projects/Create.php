<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Services\UsageLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public ?int $projectId = null;

    public string $name = '';
    public string $description = '';
    public string $environment = 'production';
    public bool $is_active = true;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $project = Project::where('team_id', Auth::user()->current_team_id)
                ->findOrFail($id);

            $this->projectId = $project->id;
            $this->name = $project->name;
            $this->description = $project->description ?? '';
            $this->environment = $project->environment;
            $this->is_active = $project->is_active;
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'environment' => ['required', Rule::in(['production', 'staging', 'development'])],
            'is_active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $team = Auth::user()->currentTeam;

        if (!$this->projectId) {
            $usageLimiter = app(UsageLimiter::class);
            if (!$usageLimiter->canCreateProject($team)) {
                session()->flash('error', 'You have reached your project limit. Please upgrade your plan.');
                return;
            }
        }

        $data = [
            'team_id' => $team->id,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'environment' => $this->environment,
            'is_active' => $this->is_active,
        ];

        if ($this->projectId) {
            $project = Project::where('team_id', $team->id)->findOrFail($this->projectId);
            $project->update($data);
            session()->flash('message', 'Project updated successfully.');
        } else {
            Project::create($data);
            session()->flash('message', 'Project created successfully.');
        }

        $this->redirect(route('projects.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.projects.create', [
            'environments' => [
                'production' => 'Production',
                'staging' => 'Staging',
                'development' => 'Development',
            ],
            'isEditing' => (bool) $this->projectId,
        ])->layout('layouts.app');
    }
}
