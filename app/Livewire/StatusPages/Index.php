<?php

declare(strict_types=1);

namespace App\Livewire\StatusPages;

use App\Models\StatusPage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $statusPage = StatusPage::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $statusPage->delete();

        session()->flash('message', 'Status page deleted successfully.');
    }

    public function togglePublic(int $id): void
    {
        $statusPage = StatusPage::where('team_id', Auth::user()->current_team_id)
            ->findOrFail($id);

        $statusPage->update(['is_public' => ! $statusPage->is_public]);
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;

        $statusPages = StatusPage::where('team_id', $team->id)
            ->with('monitors')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.status-pages.index', [
            'statusPages' => $statusPages,
        ])->layout('layouts.app');
    }
}
