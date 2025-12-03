<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use App\Models\ScheduledReport;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generateDailySummary(Team $team, ?array $config = null): array
    {
        $since = now()->subDay();

        return $this->generateSummary($team, $since, 'Daily');
    }

    public function generateWeeklySummary(Team $team, ?array $config = null): array
    {
        $since = now()->subWeek();

        return $this->generateSummary($team, $since, 'Weekly');
    }

    public function generateMonthlySummary(Team $team, ?array $config = null): array
    {
        $since = now()->subMonth();

        return $this->generateSummary($team, $since, 'Monthly');
    }

    public function generateSlaReport(Team $team, ?array $config = null): array
    {
        $since = now()->subMonth();
        $monitors = Monitor::where('team_id', $team->id)->where('is_active', true)->get();

        $slaData = $monitors->map(function ($monitor) use ($since) {
            $totalChecks = $monitor->checks()->where('checked_at', '>=', $since)->count();
            $successfulChecks = $monitor->checks()
                ->where('checked_at', '>=', $since)
                ->where('status', 'up')
                ->count();

            $uptime = $totalChecks > 0 ? round(($successfulChecks / $totalChecks) * 100, 4) : 100;

            $avgResponseTime = $monitor->checks()
                ->where('checked_at', '>=', $since)
                ->whereNotNull('response_time')
                ->avg('response_time');

            return [
                'monitor' => $monitor->name,
                'target' => $monitor->target,
                'type' => $monitor->type,
                'uptime_percentage' => $uptime,
                'total_checks' => $totalChecks,
                'successful_checks' => $successfulChecks,
                'failed_checks' => $totalChecks - $successfulChecks,
                'avg_response_time' => round($avgResponseTime ?? 0, 2),
                'sla_met' => $uptime >= ($config['sla_target'] ?? 99.9),
            ];
        });

        $overallUptime = $slaData->avg('uptime_percentage');

        return [
            'type' => 'SLA Report',
            'period' => [
                'start' => $since->format('Y-m-d H:i:s'),
                'end' => now()->format('Y-m-d H:i:s'),
            ],
            'team' => $team->name,
            'sla_target' => $config['sla_target'] ?? 99.9,
            'overall_uptime' => round($overallUptime, 4),
            'overall_sla_met' => $overallUptime >= ($config['sla_target'] ?? 99.9),
            'monitors' => $slaData->toArray(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function generateSummary(Team $team, $since, string $period): array
    {
        $monitors = Monitor::where('team_id', $team->id)->where('is_active', true)->get();

        $totalChecks = MonitorCheck::whereIn('monitor_id', $monitors->pluck('id'))
            ->where('checked_at', '>=', $since)
            ->count();

        $successfulChecks = MonitorCheck::whereIn('monitor_id', $monitors->pluck('id'))
            ->where('checked_at', '>=', $since)
            ->where('status', 'up')
            ->count();

        $avgResponseTime = MonitorCheck::whereIn('monitor_id', $monitors->pluck('id'))
            ->where('checked_at', '>=', $since)
            ->whereNotNull('response_time')
            ->avg('response_time');

        $incidents = MonitorCheck::whereIn('monitor_id', $monitors->pluck('id'))
            ->where('checked_at', '>=', $since)
            ->where('status', 'down')
            ->select('monitor_id', DB::raw('MIN(checked_at) as started_at'))
            ->groupBy('monitor_id')
            ->get();

        $monitorStats = $monitors->map(function ($monitor) use ($since) {
            $checks = $monitor->checks()->where('checked_at', '>=', $since)->count();
            $successful = $monitor->checks()
                ->where('checked_at', '>=', $since)
                ->where('status', 'up')
                ->count();

            return [
                'name' => $monitor->name,
                'type' => $monitor->type,
                'status' => $monitor->status,
                'uptime' => $checks > 0 ? round(($successful / $checks) * 100, 2) : 100,
                'checks' => $checks,
                'avg_response_time' => round($monitor->checks()
                    ->where('checked_at', '>=', $since)
                    ->whereNotNull('response_time')
                    ->avg('response_time') ?? 0, 2),
            ];
        });

        return [
            'type' => "{$period} Summary",
            'period' => [
                'start' => $since->format('Y-m-d H:i:s'),
                'end' => now()->format('Y-m-d H:i:s'),
            ],
            'team' => $team->name,
            'summary' => [
                'total_monitors' => $monitors->count(),
                'monitors_up' => $monitors->where('status', 'up')->count(),
                'monitors_down' => $monitors->where('status', 'down')->count(),
                'total_checks' => $totalChecks,
                'successful_checks' => $successfulChecks,
                'overall_uptime' => $totalChecks > 0 ? round(($successfulChecks / $totalChecks) * 100, 2) : 100,
                'avg_response_time' => round($avgResponseTime ?? 0, 2),
                'incidents_count' => $incidents->count(),
            ],
            'monitors' => $monitorStats->toArray(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
