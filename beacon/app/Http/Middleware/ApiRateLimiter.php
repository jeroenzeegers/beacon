<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\UsageLimiter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    public function __construct(
        private readonly UsageLimiter $usageLimiter
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $team = $user->currentTeam;

        if (! $team) {
            return response()->json([
                'message' => 'No team selected.',
            ], 403);
        }

        // Check if team has API access
        if (! $this->usageLimiter->hasFeature($team, 'api_access')) {
            return response()->json([
                'message' => 'API access is not available on your current plan.',
                'upgrade_url' => route('billing.plans'),
            ], 403);
        }

        // Get rate limit based on plan
        $rateLimit = $this->usageLimiter->getPlanLimit($team, 'api_rate_limit') ?? 60;

        $key = 'api:'.$team->id;

        if (RateLimiter::tooManyAttempts($key, $rateLimit)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message' => 'Too many requests.',
                'retry_after' => $seconds,
            ], 429)->withHeaders([
                'Retry-After' => $seconds,
                'X-RateLimit-Limit' => $rateLimit,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        RateLimiter::hit($key, 60);

        $response = $next($request);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $rateLimit,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $rateLimit),
        ]);
    }
}
