<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';
    public string $action = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $logs = AuditLog::query()
            ->with(['user', 'team'])
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")))
            ->when($this->action, fn ($q) => $q->where('action', $this->action))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(20);

        $actions = AuditLog::distinct()->pluck('action')->sort();

        return view('livewire.admin.audit-logs', [
            'logs' => $logs,
            'actions' => $actions,
        ]);
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'action', 'dateFrom', 'dateTo']);
    }
}
