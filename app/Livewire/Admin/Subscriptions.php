<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Subscriptions extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $subscriptions = DB::table('subscriptions')
            ->join('teams', 'subscriptions.team_id', '=', 'teams.id')
            ->select('subscriptions.*', 'teams.name as team_name')
            ->when($this->search, fn ($q) => $q->where('teams.name', 'like', "%{$this->search}%"))
            ->when($this->filter === 'active', fn ($q) => $q->where('stripe_status', 'active'))
            ->when($this->filter === 'canceled', fn ($q) => $q->where('stripe_status', 'canceled'))
            ->when($this->filter === 'past_due', fn ($q) => $q->where('stripe_status', 'past_due'))
            ->when($this->filter === 'trialing', fn ($q) => $q->where('stripe_status', 'trialing'))
            ->orderByDesc('subscriptions.created_at')
            ->paginate(15);

        $stats = [
            'total' => DB::table('subscriptions')->count(),
            'active' => DB::table('subscriptions')->where('stripe_status', 'active')->count(),
            'trialing' => DB::table('subscriptions')->where('stripe_status', 'trialing')->count(),
            'canceled' => DB::table('subscriptions')->where('stripe_status', 'canceled')->count(),
        ];

        return view('livewire.admin.subscriptions', [
            'subscriptions' => $subscriptions,
            'stats' => $stats,
        ]);
    }
}
