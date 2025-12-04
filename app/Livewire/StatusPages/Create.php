<?php

declare(strict_types=1);

namespace App\Livewire\StatusPages;

use App\Models\Monitor;
use App\Models\StatusPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public ?int $statusPageId = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public ?string $custom_domain = null;

    public ?string $logo_url = null;

    public string $primary_color = '#8B5CF6';

    public bool $is_public = true;

    public bool $show_uptime = true;

    public bool $show_response_time = true;

    public int $uptime_days_shown = 90;

    /** @var array<int> */
    public array $monitor_ids = [];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $statusPage = StatusPage::where('team_id', Auth::user()->current_team_id)
                ->with('monitors')
                ->findOrFail($id);

            $this->statusPageId = $statusPage->id;
            $this->name = $statusPage->name;
            $this->slug = $statusPage->slug;
            $this->description = $statusPage->description ?? '';
            $this->custom_domain = $statusPage->custom_domain;
            $this->logo_url = $statusPage->logo_url;
            $this->primary_color = $statusPage->primary_color ?? '#8B5CF6';
            $this->is_public = $statusPage->is_public;
            $this->show_uptime = $statusPage->show_uptime;
            $this->show_response_time = $statusPage->show_response_time;
            $this->uptime_days_shown = $statusPage->uptime_days_shown;
            $this->monitor_ids = $statusPage->monitors->pluck('id')->toArray();
        }
    }

    public function updatedName(): void
    {
        if (! $this->statusPageId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash'],
            'description' => ['nullable', 'string', 'max:1000'],
            'custom_domain' => ['nullable', 'string', 'max:255'],
            'logo_url' => ['nullable', 'url', 'max:500'],
            'primary_color' => ['required', 'string', 'max:7'],
            'is_public' => ['boolean'],
            'show_uptime' => ['boolean'],
            'show_response_time' => ['boolean'],
            'uptime_days_shown' => ['required', 'integer', 'min:7', 'max:365'],
            'monitor_ids' => ['array'],
            'monitor_ids.*' => ['exists:monitors,id'],
        ]);

        $team = Auth::user()->currentTeam;

        $data = [
            'team_id' => $team->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description ?: null,
            'custom_domain' => $this->custom_domain ?: null,
            'logo_url' => $this->logo_url ?: null,
            'primary_color' => $this->primary_color,
            'is_public' => $this->is_public,
            'show_uptime' => $this->show_uptime,
            'show_response_time' => $this->show_response_time,
            'uptime_days_shown' => $this->uptime_days_shown,
        ];

        if ($this->statusPageId) {
            $statusPage = StatusPage::where('team_id', $team->id)->findOrFail($this->statusPageId);
            $statusPage->update($data);
            $message = 'Status page updated successfully.';
        } else {
            $statusPage = StatusPage::create($data);
            $message = 'Status page created successfully.';
        }

        // Sync monitors with sort order
        $syncData = [];
        foreach ($this->monitor_ids as $index => $monitorId) {
            $syncData[$monitorId] = ['sort_order' => $index];
        }
        $statusPage->monitors()->sync($syncData);

        session()->flash('message', $message);
        $this->redirect(route('status-pages.index'), navigate: true);
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        return view('livewire.status-pages.create', [
            'monitors' => Monitor::where('team_id', $team->id)->orderBy('name')->get(),
            'isEditing' => $this->statusPageId !== null,
        ])->layout('layouts.app');
    }
}
