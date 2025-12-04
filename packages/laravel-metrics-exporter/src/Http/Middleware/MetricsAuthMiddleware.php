<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MetricsAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authType = config('metrics-exporter.auth.type', 'token');

        if ($authType === 'none') {
            return $next($request);
        }

        $authenticated = match ($authType) {
            'token' => $this->validateToken($request),
            'basic' => $this->validateBasicAuth($request),
            default => false,
        };

        if (! $authenticated) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing authentication credentials.',
            ], 401);
        }

        return $next($request);
    }

    private function validateToken(Request $request): bool
    {
        $configuredToken = config('metrics-exporter.auth.token');

        if (empty($configuredToken)) {
            // If no token is configured, deny access for security
            return false;
        }

        // Check Bearer token in Authorization header
        $bearerToken = $request->bearerToken();
        if ($bearerToken && hash_equals($configuredToken, $bearerToken)) {
            return true;
        }

        // Also check query parameter as fallback
        $queryToken = $request->query('token');
        if ($queryToken && hash_equals($configuredToken, $queryToken)) {
            return true;
        }

        return false;
    }

    private function validateBasicAuth(Request $request): bool
    {
        $configuredUsername = config('metrics-exporter.auth.username');
        $configuredPassword = config('metrics-exporter.auth.password');

        if (empty($configuredUsername) || empty($configuredPassword)) {
            return false;
        }

        $username = $request->getUser();
        $password = $request->getPassword();

        if (empty($username) || empty($password)) {
            return false;
        }

        return hash_equals($configuredUsername, $username)
            && hash_equals($configuredPassword, $password);
    }
}
