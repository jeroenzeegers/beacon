<?php

declare(strict_types=1);

namespace App\Livewire\Incidents;

use App\Models\Incident;
use App\Models\Monitor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public ?int $incidentId = null;

    public string $title = '';

    public string $description = '';

    public string $status = 'investigating';

    public string $severity = 'minor';

    public ?int $monitor_id = null;

    public bool $is_public = true;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $incident = Incident::where('team_id', Auth::user()->current_team_id)
                ->findOrFail($id);

            $this->incidentId = $incident->id;
            $this->title = $incident->title;
            $this->description = $incident->description ?? '';
            $this->status = $incident->status;
            $this->severity = $incident->severity;
            $this->monitor_id = $incident->monitor_id;
            $this->is_public = $incident->is_public;
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(array_keys(Incident::getStatuses()))],
            'severity' => ['required', Rule::in(array_keys(Incident::getSeverities()))],
            'monitor_id' => ['nullable', 'exists:monitors,id'],
            'is_public' => ['boolean'],
        ]);

        $team = Auth::user()->currentTeam;

        $data = [
            'team_id' => $team->id,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'status' => $this->status,
            'severity' => $this->severity,
            'monitor_id' => $this->monitor_id,
            'is_public' => $this->is_public,
        ];

        if ($this->incidentId) {
            $incident = Incident::where('team_id', $team->id)->findOrFail($this->incidentId);
            $incident->update($data);

            if ($this->status === 'resolved' && ! $incident->resolved_at) {
                $incident->update(['resolved_at' => now()]);
            }

            $message = 'Incident updated successfully.';
        } else {
            $data['started_at'] = now();
            Incident::create($data);
            $message = 'Incident created successfully.';
        }

        session()->flash('message', $message);
        $this->redirect(route('incidents.index'), navigate: true);
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        return view('livewire.incidents.create', [
            'monitors' => Monitor::where('team_id', $team->id)->orderBy('name')->get(),
            'statuses' => Incident::getStatuses(),
            'severities' => Incident::getSeverities(),
            'isEditing' => $this->incidentId !== null,
        ])->layout('layouts.app');
    }
}
