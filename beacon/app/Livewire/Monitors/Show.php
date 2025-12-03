<?php

declare(strict_types=1);

namespace App\Livewire\Monitors;

use App\Jobs\PerformMonitorCheck;
use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Monitor $monitor;

    public int $chartDays = 7;

    /**
     * Get the listeners for the component.
     */
    public function getListeners(): array
    {
        return [
            "echo-private:monitor.{$this->monitor->id},monitor.status.changed" => 'handleStatusChange',
            "echo-private:monitor.{$this->monitor->id},monitor.check.completed" => 'handleCheckCompleted',
        ];
    }

    /**
     * Handle monitor status change.
     */
    public function handleStatusChange(array $data): void
    {
        $this->monitor->refresh();

        $this->dispatch('notify', [
            'type' => $data['monitor']['status'] === 'up' ? 'success' : 'error',
            'message' => "Monitor is now {$data['monitor']['status']}",
        ]);
    }

    /**
     * Handle check completed.
     */
    public function handleCheckCompleted(array $data): void
    {
        $this->monitor->refresh();
        $this->resetPage();
    }

    public function mount(int $id): void
    {
        $this->monitor = Monitor::where('team_id', Auth::user()->current_team_id)
            ->with(['project', 'latestCheck'])
            ->findOrFail($id);
    }

    public function checkNow(): void
    {
        PerformMonitorCheck::dispatch($this->monitor);

        session()->flash('message', 'Check scheduled. Results will appear shortly.');
    }

    public function toggleActive(): void
    {
        $this->monitor->update(['is_active' => !$this->monitor->is_active]);
        $this->monitor->refresh();
    }

    public function delete(): void
    {
        $this->monitor->delete();

        session()->flash('message', 'Monitor deleted successfully.');

        $this->redirect(route('monitors.index'), navigate: true);
    }

    public function render()
    {
        $checks = MonitorCheck::where('monitor_id', $this->monitor->id)
            ->orderBy('checked_at', 'desc')
            ->paginate(20);

        // Get uptime data for chart
        $uptimeData = $this->getUptimeChartData();

        // Get response time data for chart
        $responseTimeData = $this->getResponseTimeChartData();

        return view('livewire.monitors.show', [
            'checks' => $checks,
            'uptimePercentage' => $this->monitor->getUptimePercentage($this->chartDays),
            'avgResponseTime' => $this->monitor->getAverageResponseTime($this->chartDays),
            'uptimeData' => $uptimeData,
            'responseTimeData' => $responseTimeData,
        ])->layout('layouts.app');
    }

    private function getUptimeChartData(): array
    {
        $data = [];
        $startDate = now()->subDays($this->chartDays);

        for ($i = 0; $i < $this->chartDays; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayStart = $date->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $checks = MonitorCheck::where('monitor_id', $this->monitor->id)
                ->whereBetween('checked_at', [$dayStart, $dayEnd])
                ->get();

            $total = $checks->count();
            $up = $checks->where('status', Monitor::STATUS_UP)->count();

            $data[] = [
                'date' => $date->format('M d'),
                'uptime' => $total > 0 ? round(($up / $total) * 100, 2) : 100,
            ];
        }

        return $data;
    }

    private function getResponseTimeChartData(): array
    {
        $data = [];
        $startDate = now()->subDays($this->chartDays);

        for ($i = 0; $i < $this->chartDays; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayStart = $date->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $avg = MonitorCheck::where('monitor_id', $this->monitor->id)
                ->whereBetween('checked_at', [$dayStart, $dayEnd])
                ->whereNotNull('response_time')
                ->avg('response_time');

            $data[] = [
                'date' => $date->format('M d'),
                'response_time' => $avg ? round($avg, 2) : null,
            ];
        }

        return $data;
    }
}
