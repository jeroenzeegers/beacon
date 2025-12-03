<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\UsageLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function __construct(
        private readonly UsageLimiter $usageLimiter
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $team = $request->user()->currentTeam;

        $projects = Project::where('team_id', $team->id)
            ->withCount('monitors')
            ->orderBy('name')
            ->paginate($request->input('per_page', 15));

        return ProjectResource::collection($projects);
    }

    public function store(Request $request): JsonResponse
    {
        $team = $request->user()->currentTeam;

        if (!$this->usageLimiter->canCreateProject($team)) {
            return response()->json([
                'message' => 'Project limit reached. Please upgrade your plan.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'environment' => ['nullable', 'string', 'in:production,staging,development'],
        ]);

        $project = Project::create([
            ...$validated,
            'team_id' => $team->id,
        ]);

        return response()->json([
            'message' => 'Project created successfully.',
            'data' => new ProjectResource($project),
        ], 201);
    }

    public function show(Request $request, int $id): ProjectResource
    {
        $team = $request->user()->currentTeam;

        $project = Project::where('team_id', $team->id)
            ->withCount('monitors')
            ->findOrFail($id);

        return new ProjectResource($project);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $project = Project::where('team_id', $team->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'environment' => ['nullable', 'string', 'in:production,staging,development'],
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Project updated successfully.',
            'data' => new ProjectResource($project),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $team = $request->user()->currentTeam;

        $project = Project::where('team_id', $team->id)->findOrFail($id);

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }
}
