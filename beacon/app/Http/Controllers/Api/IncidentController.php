<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\IncidentResource;
use App\Models\Incident;
use App\Models\IncidentUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class IncidentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $team = $request->user()->currentTeam;

        $query = Incident::where('team_id', $team->id)
            ->with(['monitor', 'updates']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('monitor_id')) {
            $query->where('monitor_id', $request->input('monitor_id'));
        }

        if ($request->boolean('active')) {
            $query->active();
        }

        $incidents = $query->orderBy('started_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return IncidentResource::collection($incidents);
    }

    public function store(Request $request): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(Incident::STATUSES)],
            'severity' => ['required', 'string', Rule::in(Incident::SEVERITIES)],
            'monitor_id' => ['nullable', 'integer', 'exists:monitors,id'],
            'started_at' => ['nullable', 'date'],
        ]);

        // Validate monitor belongs to team
        if (isset($validated['monitor_id'])) {
            $monitorExists = $team->monitors()->where('id', $validated['monitor_id'])->exists();
            if (!$monitorExists) {
                return response()->json([
                    'message' => 'The selected monitor does not exist.',
                ], 422);
            }
        }

        $incident = Incident::create([
            ...$validated,
            'team_id' => $team->id,
            'started_at' => $validated['started_at'] ?? now(),
        ]);

        // Create initial update
        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'status' => $incident->status,
            'message' => $validated['description'] ?? 'Incident created.',
        ]);

        return response()->json([
            'message' => 'Incident created successfully.',
            'data' => new IncidentResource($incident->load(['monitor', 'updates'])),
        ], 201);
    }

    public function show(Request $request, int $id): IncidentResource
    {
        $team = $request->user()->currentTeam;

        $incident = Incident::where('team_id', $team->id)
            ->with(['monitor', 'updates'])
            ->findOrFail($id);

        return new IncidentResource($incident);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $incident = Incident::where('team_id', $team->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', Rule::in(Incident::STATUSES)],
            'severity' => ['sometimes', 'required', 'string', Rule::in(Incident::SEVERITIES)],
        ]);

        $statusChanged = isset($validated['status']) && $validated['status'] !== $incident->status;

        // Auto-set resolved_at
        if ($statusChanged && $validated['status'] === Incident::STATUS_RESOLVED) {
            $validated['resolved_at'] = now();
        }

        $incident->update($validated);

        // Create status update if status changed
        if ($statusChanged) {
            IncidentUpdate::create([
                'incident_id' => $incident->id,
                'status' => $validated['status'],
                'message' => 'Status changed to ' . $validated['status'],
            ]);
        }

        return response()->json([
            'message' => 'Incident updated successfully.',
            'data' => new IncidentResource($incident->load(['monitor', 'updates'])),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $incident = Incident::where('team_id', $team->id)->findOrFail($id);

        $incident->delete();

        return response()->json([
            'message' => 'Incident deleted successfully.',
        ]);
    }

    public function addUpdate(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $incident = Incident::where('team_id', $team->id)->findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(Incident::STATUSES)],
            'message' => ['required', 'string'],
        ]);

        $update = IncidentUpdate::create([
            'incident_id' => $incident->id,
            'status' => $validated['status'],
            'message' => $validated['message'],
        ]);

        // Update incident status
        $incident->update([
            'status' => $validated['status'],
            'resolved_at' => $validated['status'] === Incident::STATUS_RESOLVED ? now() : null,
        ]);

        return response()->json([
            'message' => 'Update added successfully.',
            'data' => new IncidentResource($incident->load(['monitor', 'updates'])),
        ], 201);
    }

    public function resolve(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $incident = Incident::where('team_id', $team->id)->findOrFail($id);

        if ($incident->status === Incident::STATUS_RESOLVED) {
            return response()->json([
                'message' => 'Incident is already resolved.',
            ], 422);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string'],
        ]);

        $incident->update([
            'status' => Incident::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'status' => Incident::STATUS_RESOLVED,
            'message' => $validated['message'] ?? 'Incident resolved.',
        ]);

        return response()->json([
            'message' => 'Incident resolved successfully.',
            'data' => new IncidentResource($incident->load(['monitor', 'updates'])),
        ]);
    }
}
