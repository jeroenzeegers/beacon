<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Monitor;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BadgeController extends Controller
{
    public function __construct(
        private BadgeService $badgeService
    ) {}

    public function uptime(Request $request, int $monitorId): Response
    {
        $monitor = Monitor::findOrFail($monitorId);

        // Check if monitor allows public badges
        if (! ($monitor->metadata['public_badge'] ?? false)) {
            abort(403, 'Public badges not enabled for this monitor');
        }

        $style = $request->input('style', 'flat');
        $svg = $this->badgeService->generateUptimeBadge($monitor, $style);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function status(Request $request, int $monitorId): Response
    {
        $monitor = Monitor::findOrFail($monitorId);

        if (! ($monitor->metadata['public_badge'] ?? false)) {
            abort(403, 'Public badges not enabled for this monitor');
        }

        $style = $request->input('style', 'flat');
        $svg = $this->badgeService->generateStatusBadge($monitor, $style);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function responseTime(Request $request, int $monitorId): Response
    {
        $monitor = Monitor::findOrFail($monitorId);

        if (! ($monitor->metadata['public_badge'] ?? false)) {
            abort(403, 'Public badges not enabled for this monitor');
        }

        $style = $request->input('style', 'flat');
        $svg = $this->badgeService->generateResponseTimeBadge($monitor, $style);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
