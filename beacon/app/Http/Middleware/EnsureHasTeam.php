<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasTeam
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // If user doesn't have a current team, try to set one
        if (!$user->current_team_id) {
            $firstTeam = $user->teams()->first();

            if ($firstTeam) {
                $user->update(['current_team_id' => $firstTeam->id]);
            } else {
                // Create a default team for the user
                $user->createTeam($user->name . "'s Team");
            }
        }

        // Ensure the user has access to their current team
        if ($user->current_team_id && !$user->belongsToTeam($user->currentTeam)) {
            // Team access was revoked, switch to another team or create one
            $firstTeam = $user->teams()->first();

            if ($firstTeam) {
                $user->update(['current_team_id' => $firstTeam->id]);
            } else {
                $user->createTeam($user->name . "'s Team");
            }
        }

        return $next($request);
    }
}
