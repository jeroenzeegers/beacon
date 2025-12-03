<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LiveStatus extends Component
{
    public array $monitors = [];
    public array $statusCounts = [];
    public array $responseTimeData = [];
    public array $uptimeData = [];
    public array $recentChecks = [];
    public int $totalChecksToday = 0;
    public float $averageResponseTime = 0;
    public float $overallUptime = 0;

    public function mount(): void
    {
        $this->loadData();
    }

    public function getListeners(): array
    {
        $teamId = Auth::user()->current_team_id;

        return [
            "echo-private:team.{$teamId},monitor.status.changed" => 'handleMonitorStatusChange',
            "echo-private:team.{$teamId},monitor.check.completed" => 'handleCheckCompleted',
        ];
    }

    public function handleMonitorStatusChange(array $data): void
    {
        $this->loadData();
        $this->dispatch('statusChanged', $data);
    }

    public function handleCheckCompleted(array $data): void
    {
        $this->loadData();
        $this->dispatch('checkCompleted', $data);
    }

    public function loadData(): void
    {
        $teamId = Auth::user()->current_team_id;

        // Load monitors with latest check
        $this->monitors = Monitor::where('team_id', $teamId)
            ->where('is_active', true)
            ->with(['latestCheck'])
            ->orderBy('name')
            ->get()
            ->map(function ($monitor) {
                return [
                    'id' => $monitor->id,
                    'name' => $monitor->name,
                    'url' => $monitor->url,
                    'type' => $monitor->type,
                    'status' => $monitor->status,
                    'response_time' => $monitor->latestCheck?->response_time,
                    'last_checked_at' => $monitor->latestCheck?->created_at?->diffForHumans(),
                    'uptime_percentage' => $this->calculateUptimePercentage($monitor),
                ];
            })
            ->toArray();

        // Status counts for pie chart
        $this->statusCounts = Monitor::where('team_id', $teamId)
            ->where('is_active', true)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Response time data for line chart (last 24 hours)
        $this->loadResponseTimeData($teamId);

        // Uptime data for bar chart
        $this->loadUptimeData($teamId);

        // Recent checks
        $this->recentChecks = MonitorCheck::whereHas('monitor', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })
            ->with('monitor:id,name,type')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($check) {
                return [
                    'id' => $check->id,
                    'monitor_name' => $check->monitor->name,
                    'monitor_type' => $check->monitor->type,
                    'status' => $check->status,
                    'response_time' => $check->response_time,
                    'created_at' => $check->created_at->diffForHumans(),
                ];
            })
            ->toArray();

        // Stats
        $this->totalChecksToday = MonitorCheck::whereHas('monitor', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })
            ->whereDate('checked_at', today())
            ->count();

        $this->averageResponseTime = round((float) (MonitorCheck::whereHas('monitor', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })
            ->whereDate('checked_at', today())
            ->whereNotNull('response_time')
            ->avg('response_time') ?? 0), 2);

        $this->calculateOverallUptime($teamId);
    }

    private function calculateUptimePercentage(Monitor $monitor): float
    {
        $totalChecks = $monitor->checks()
            ->where('checked_at', '>=', now()->subDays(30))
            ->count();

        if ($totalChecks === 0) {
            return 100.0;
        }

        $successfulChecks = $monitor->checks()
            ->where('checked_at', '>=', now()->subDays(30))
            ->where('status', 'up')
            ->count();

        return round(($successfulChecks / $totalChecks) * 100, 2);
    }

    private function loadResponseTimeData(int $teamId): void
    {
        $monitorIds = Monitor::where('team_id', $teamId)
            ->where('is_active', true)
            ->pluck('id');

        // Get hourly averages for last 24 hours (PostgreSQL compatible)
        $this->responseTimeData = MonitorCheck::whereIn('monitor_id', $monitorIds)
            ->where('checked_at', '>=', now()->subHours(24))
            ->whereNotNull('response_time')
            ->select(
                DB::raw("TO_CHAR(checked_at, 'YYYY-MM-DD HH24:00:00') as hour"),
                DB::raw('AVG(response_time) as avg_response_time'),
                DB::raw('MIN(response_time) as min_response_time'),
                DB::raw('MAX(response_time) as max_response_time')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => $item->hour,
                    'label' => \Carbon\Carbon::parse($item->hour)->format('H:i'),
                    'avg' => round((float) $item->avg_response_time, 2),
                    'min' => round((float) $item->min_response_time, 2),
                    'max' => round((float) $item->max_response_time, 2),
                ];
            })
            ->toArray();
    }

    private function loadUptimeData(int $teamId): void
    {
        $monitors = Monitor::where('team_id', $teamId)
            ->where('is_active', true)
            ->get();

        $this->uptimeData = $monitors->map(function ($monitor) {
            return [
                'name' => $monitor->name,
                'uptime' => $this->calculateUptimePercentage($monitor),
            ];
        })->toArray();
    }

    private function calculateOverallUptime(int $teamId): void
    {
        $monitorIds = Monitor::where('team_id', $teamId)
            ->where('is_active', true)
            ->pluck('id');

        $totalChecks = MonitorCheck::whereIn('monitor_id', $monitorIds)
            ->where('checked_at', '>=', now()->subDays(30))
            ->count();

        if ($totalChecks === 0) {
            $this->overallUptime = 100.0;
            return;
        }

        $successfulChecks = MonitorCheck::whereIn('monitor_id', $monitorIds)
            ->where('checked_at', '>=', now()->subDays(30))
            ->where('status', 'up')
            ->count();

        $this->overallUptime = round(($successfulChecks / $totalChecks) * 100, 2);
    }

    public function render()
    {
        return view('livewire.live-status')
            ->layout('layouts.app', ['title' => 'Live Status']);
    }
}
