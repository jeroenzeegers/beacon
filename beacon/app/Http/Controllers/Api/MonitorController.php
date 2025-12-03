<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\MonitorCheckResource;
use App\Http\Resources\MonitorResource;
use App\Jobs\PerformMonitorCheck;
use App\Models\Monitor;
use App\Services\UsageLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class MonitorController extends Controller
{
    public function __construct(
        private readonly UsageLimiter $usageLimiter
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $team = $request->user()->currentTeam;

        $query = Monitor::where('team_id', $team->id)
            ->with(['project', 'latestCheck']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        $monitors = $query->orderBy('name')
            ->paginate($request->input('per_page', 15));

        return MonitorResource::collection($monitors);
    }

    public function store(Request $request): JsonResponse
    {
        $team = $request->user()->currentTeam;

        if (!$this->usageLimiter->canCreateMonitor($team)) {
            return response()->json([
                'message' => 'Monitor limit reached. Please upgrade your plan.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(Monitor::TYPES)],
            'url' => ['required_if:type,http,https', 'nullable', 'url'],
            'host' => ['required_if:type,tcp,ping,ssl_expiry', 'nullable', 'string'],
            'port' => ['required_if:type,tcp', 'nullable', 'integer', 'min:1', 'max:65535'],
            'check_interval' => ['nullable', 'integer', 'min:60', 'max:86400'],
            'timeout' => ['nullable', 'integer', 'min:1', 'max:120'],
            'http_method' => ['nullable', 'string', Rule::in(['GET', 'POST', 'HEAD'])],
            'expected_status_code' => ['nullable', 'integer', 'min:100', 'max:599'],
            'verify_ssl' => ['nullable', 'boolean'],
            'ssl_expiry_threshold' => ['nullable', 'integer', 'min:1', 'max:365'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        // Validate project belongs to team
        if (isset($validated['project_id'])) {
            $projectExists = $team->projects()->where('id', $validated['project_id'])->exists();
            if (!$projectExists) {
                return response()->json([
                    'message' => 'The selected project does not exist.',
                ], 422);
            }
        }

        // Build settings array
        $settings = [];
        foreach (['http_method', 'expected_status_code', 'timeout', 'verify_ssl', 'ssl_expiry_threshold'] as $key) {
            if (isset($validated[$key])) {
                $settings[$key] = $validated[$key];
                unset($validated[$key]);
            }
        }

        $monitor = Monitor::create([
            ...$validated,
            'team_id' => $team->id,
            'settings' => $settings ?: null,
            'is_enabled' => $validated['is_enabled'] ?? true,
            'check_interval' => $validated['check_interval'] ?? 300,
        ]);

        // Trigger initial check
        if ($monitor->is_enabled) {
            PerformMonitorCheck::dispatch($monitor);
        }

        return response()->json([
            'message' => 'Monitor created successfully.',
            'data' => new MonitorResource($monitor),
        ], 201);
    }

    public function show(Request $request, int $id): MonitorResource
    {
        $team = $request->user()->currentTeam;

        $monitor = Monitor::where('team_id', $team->id)
            ->with(['project', 'latestCheck'])
            ->findOrFail($id);

        return new MonitorResource($monitor);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $monitor = Monitor::where('team_id', $team->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'url' => ['nullable', 'url'],
            'host' => ['nullable', 'string'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'check_interval' => ['nullable', 'integer', 'min:60', 'max:86400'],
            'timeout' => ['nullable', 'integer', 'min:1', 'max:120'],
            'http_method' => ['nullable', 'string', Rule::in(['GET', 'POST', 'HEAD'])],
            'expected_status_code' => ['nullable', 'integer', 'min:100', 'max:599'],
            'verify_ssl' => ['nullable', 'boolean'],
            'ssl_expiry_threshold' => ['nullable', 'integer', 'min:1', 'max:365'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        // Build settings array
        $settings = $monitor->settings ?? [];
        foreach (['http_method', 'expected_status_code', 'timeout', 'verify_ssl', 'ssl_expiry_threshold'] as $key) {
            if (array_key_exists($key, $validated)) {
                $settings[$key] = $validated[$key];
                unset($validated[$key]);
            }
        }
        $validated['settings'] = $settings ?: null;

        $monitor->update($validated);

        return response()->json([
            'message' => 'Monitor updated successfully.',
            'data' => new MonitorResource($monitor->fresh()),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $monitor = Monitor::where('team_id', $team->id)->findOrFail($id);

        $monitor->delete();

        return response()->json([
            'message' => 'Monitor deleted successfully.',
        ]);
    }

    public function triggerCheck(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $monitor = Monitor::where('team_id', $team->id)->findOrFail($id);

        if (!$monitor->is_enabled) {
            return response()->json([
                'message' => 'Monitor is disabled.',
            ], 422);
        }

        PerformMonitorCheck::dispatch($monitor);

        return response()->json([
            'message' => 'Check triggered successfully.',
        ]);
    }

    public function checks(Request $request, int $id): AnonymousResourceCollection
    {
        $team = $request->user()->currentTeam;

        $monitor = Monitor::where('team_id', $team->id)->findOrFail($id);

        $checks = $monitor->checks()
            ->orderBy('checked_at', 'desc')
            ->paginate($request->input('per_page', 50));

        return MonitorCheckResource::collection($checks);
    }

    public function stats(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $monitor = Monitor::where('team_id', $team->id)->findOrFail($id);

        $period = $request->input('period', '24h');
        $since = match ($period) {
            '1h' => now()->subHour(),
            '24h' => now()->subDay(),
            '7d' => now()->subWeek(),
            '30d' => now()->subMonth(),
            default => now()->subDay(),
        };

        $checks = $monitor->checks()
            ->where('checked_at', '>=', $since)
            ->get();

        $totalChecks = $checks->count();
        $successfulChecks = $checks->where('is_successful', true)->count();
        $avgResponseTime = $checks->avg('response_time');

        return response()->json([
            'data' => [
                'period' => $period,
                'total_checks' => $totalChecks,
                'successful_checks' => $successfulChecks,
                'failed_checks' => $totalChecks - $successfulChecks,
                'uptime_percentage' => $totalChecks > 0
                    ? round(($successfulChecks / $totalChecks) * 100, 2)
                    : 0,
                'avg_response_time' => round($avgResponseTime ?? 0),
                'min_response_time' => $checks->min('response_time'),
                'max_response_time' => $checks->max('response_time'),
            ],
        ]);
    }
}
