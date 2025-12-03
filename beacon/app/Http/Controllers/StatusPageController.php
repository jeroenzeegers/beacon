<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\StatusPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusPageController extends Controller
{
    public function show(string $slug): View
    {
        $statusPage = StatusPage::where('slug', $slug)
            ->public()
            ->with(['monitors' => function ($query) {
                $query->select(['monitors.id', 'monitors.name', 'monitors.status', 'monitors.type', 'monitors.last_check_at']);
            }])
            ->firstOrFail();

        $uptimeData = [];
        foreach ($statusPage->monitors as $monitor) {
            $uptimeData[$monitor->id] = [
                'uptime_percentage' => $monitor->getUptimePercentage($statusPage->uptime_days_shown),
                'average_response_time' => $monitor->getAverageResponseTime($statusPage->uptime_days_shown),
            ];
        }

        return view('status-page.show', [
            'statusPage' => $statusPage,
            'monitors' => $statusPage->monitors,
            'overallStatus' => $statusPage->overall_status,
            'activeIncidents' => $statusPage->active_incidents,
            'recentIncidents' => $statusPage->recent_incidents,
            'uptimePercentage' => $statusPage->getUptimePercentage(),
            'uptimeData' => $uptimeData,
        ]);
    }

    public function showByDomain(Request $request): View
    {
        $domain = $request->getHost();

        $statusPage = StatusPage::where('custom_domain', $domain)
            ->public()
            ->with(['monitors' => function ($query) {
                $query->select(['monitors.id', 'monitors.name', 'monitors.status', 'monitors.type', 'monitors.last_check_at']);
            }])
            ->firstOrFail();

        $uptimeData = [];
        foreach ($statusPage->monitors as $monitor) {
            $uptimeData[$monitor->id] = [
                'uptime_percentage' => $monitor->getUptimePercentage($statusPage->uptime_days_shown),
                'average_response_time' => $monitor->getAverageResponseTime($statusPage->uptime_days_shown),
            ];
        }

        return view('status-page.show', [
            'statusPage' => $statusPage,
            'monitors' => $statusPage->monitors,
            'overallStatus' => $statusPage->overall_status,
            'activeIncidents' => $statusPage->active_incidents,
            'recentIncidents' => $statusPage->recent_incidents,
            'uptimePercentage' => $statusPage->getUptimePercentage(),
            'uptimeData' => $uptimeData,
        ]);
    }

    public function apiStatus(string $slug)
    {
        $statusPage = StatusPage::where('slug', $slug)
            ->public()
            ->with('monitors:id,name,status,type,last_check_at')
            ->firstOrFail();

        return response()->json([
            'status' => $statusPage->overall_status,
            'uptime_percentage' => $statusPage->getUptimePercentage(),
            'monitors' => $statusPage->monitors->map(function ($monitor) use ($statusPage) {
                return [
                    'name' => $monitor->pivot->display_name ?? $monitor->name,
                    'status' => $monitor->status,
                    'uptime_percentage' => $monitor->getUptimePercentage($statusPage->uptime_days_shown),
                ];
            }),
            'active_incidents' => $statusPage->active_incidents->map(function ($incident) {
                return [
                    'title' => $incident->title,
                    'status' => $incident->status,
                    'severity' => $incident->severity,
                    'started_at' => $incident->started_at->toIso8601String(),
                ];
            }),
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}
