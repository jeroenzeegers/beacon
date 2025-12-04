<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\TeamResource;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function show(Request $request): TeamResource
    {
        $team = $request->user()->currentTeam;

        $team->load(['users', 'owner']);

        return new TeamResource($team);
    }
}
