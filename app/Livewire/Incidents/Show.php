<?php

declare(strict_types=1);

namespace App\Livewire\Incidents;

use App\Models\Incident;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    public Incident $incident;

    public string $updateStatus = '';

    public string $updateMessage = '';

    public function mount(int $id): void
    {
        $this->incident = Incident::where('team_id', Auth::user()->current_team_id)
            ->with(['monitor', 'updates.user'])
            ->findOrFail($id);

        $this->updateStatus = $this->incident->status;
    }

    public function addUpdate(): void
    {
        $this->validate([
            'updateStatus' => ['required', Rule::in(array_keys(Incident::getStatuses()))],
            'updateMessage' => ['required', 'string', 'max:1000'],
        ]);

        $this->incident->updateStatus(
            $this->updateStatus,
            $this->updateMessage,
            Auth::user()
        );

        $this->incident->refresh();
        $this->updateMessage = '';

        session()->flash('message', 'Update added successfully.');
    }

    public function resolve(): void
    {
        $this->incident->resolve();
        $this->incident->refresh();

        session()->flash('message', 'Incident marked as resolved.');
    }

    public function render()
    {
        return view('livewire.incidents.show', [
            'statuses' => Incident::getStatuses(),
        ])->layout('layouts.app');
    }
}
