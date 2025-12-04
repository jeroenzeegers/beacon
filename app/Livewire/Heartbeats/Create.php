<?php

declare(strict_types=1);

namespace App\Livewire\Heartbeats;

use App\Models\Heartbeat;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public ?int $heartbeatId = null;

    public string $name = '';

    public string $description = '';

    public ?int $project_id = null;

    public int $expected_interval = 60;

    public int $grace_period = 5;

    public bool $is_active = true;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $heartbeat = Heartbeat::where('team_id', Auth::user()->current_team_id)
                ->findOrFail($id);

            $this->heartbeatId = $heartbeat->id;
            $this->name = $heartbeat->name;
            $this->description = $heartbeat->description ?? '';
            $this->project_id = $heartbeat->project_id;
            $this->expected_interval = $heartbeat->expected_interval;
            $this->grace_period = $heartbeat->grace_period;
            $this->is_active = $heartbeat->is_active;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
            'expected_interval' => 'required|integer|min:1|max:10080',
            'grace_period' => 'required|integer|min:1|max:60',
            'is_active' => 'boolean',
        ]);

        if ($this->heartbeatId) {
            $heartbeat = Heartbeat::where('team_id', Auth::user()->current_team_id)
                ->findOrFail($this->heartbeatId);
            $heartbeat->update($validated);
            session()->flash('success', 'Heartbeat updated successfully.');
        } else {
            $heartbeat = Heartbeat::create([
                ...$validated,
                'team_id' => Auth::user()->current_team_id,
            ]);
            session()->flash('success', 'Heartbeat created successfully.');
        }

        $this->redirect(route('heartbeats.show', $heartbeat->id));
    }

    public function render()
    {
        $projects = Project::where('team_id', Auth::user()->current_team_id)
            ->orderBy('name')
            ->get();

        return view('livewire.heartbeats.create', [
            'projects' => $projects,
            'isEdit' => (bool) $this->heartbeatId,
        ])->layout('layouts.app', ['title' => $this->heartbeatId ? 'Edit Heartbeat' : 'Create Heartbeat']);
    }
}
