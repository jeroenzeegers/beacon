<?php

declare(strict_types=1);

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceWindow;
use App\Models\Monitor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public ?int $windowId = null;
    public string $name = '';
    public string $description = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public bool $suppress_alerts = true;
    public bool $show_on_status_page = true;
    public array $selected_monitors = [];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $window = MaintenanceWindow::where('team_id', Auth::user()->current_team_id)
                ->with('monitors')
                ->findOrFail($id);

            $this->windowId = $window->id;
            $this->name = $window->name;
            $this->description = $window->description ?? '';
            $this->starts_at = $window->starts_at->format('Y-m-d\TH:i');
            $this->ends_at = $window->ends_at->format('Y-m-d\TH:i');
            $this->suppress_alerts = $window->suppress_alerts;
            $this->show_on_status_page = $window->show_on_status_page;
            $this->selected_monitors = $window->monitors->pluck('id')->toArray();
        } else {
            $this->starts_at = now()->addHour()->format('Y-m-d\TH:i');
            $this->ends_at = now()->addHours(2)->format('Y-m-d\TH:i');
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'suppress_alerts' => 'boolean',
            'show_on_status_page' => 'boolean',
            'selected_monitors' => 'array',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'suppress_alerts' => $validated['suppress_alerts'],
            'show_on_status_page' => $validated['show_on_status_page'],
        ];

        if ($this->windowId) {
            $window = MaintenanceWindow::where('team_id', Auth::user()->current_team_id)
                ->findOrFail($this->windowId);
            $window->update($data);
        } else {
            $window = MaintenanceWindow::create([
                ...$data,
                'team_id' => Auth::user()->current_team_id,
                'status' => MaintenanceWindow::STATUS_SCHEDULED,
            ]);
        }

        $window->monitors()->sync($this->selected_monitors);

        session()->flash('success', $this->windowId ? 'Maintenance window updated.' : 'Maintenance window scheduled.');
        $this->redirect(route('maintenance.index'));
    }

    public function render()
    {
        $monitors = Monitor::where('team_id', Auth::user()->current_team_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.maintenance.create', [
            'monitors' => $monitors,
            'isEdit' => (bool) $this->windowId,
        ])->layout('layouts.app', ['title' => $this->windowId ? 'Edit Maintenance' : 'Schedule Maintenance']);
    }
}
