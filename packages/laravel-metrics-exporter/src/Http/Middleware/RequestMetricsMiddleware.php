<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Http\Middleware;

use Beacon\MetricsExporter\Storage\StorageInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestMetricsMiddleware
{
    public function __construct(
        private StorageInterface $storage
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Skip if metrics collection is disabled
        if (!config('metrics-exporter.enabled', true)) {
            return $next($request);
        }

        // Skip ignored paths
        if ($this->shouldIgnore($request)) {
            return $next($request);
        }

        $startTime = microtime(true);

        $response = $next($request);

        $responseTimeMs = (microtime(true) - $startTime) * 1000;
        $statusCode = $response->getStatusCode();
        $route = $this->getRouteName($request);

        $this->storage->recordRequest($statusCode, $responseTimeMs, $route);

        return $response;
    }

    private function shouldIgnore(Request $request): bool
    {
        $path = $request->path();
        $ignorePaths = config('metrics-exporter.requests.ignore_paths', []);

        foreach ($ignorePaths as $pattern) {
            if (Str::is($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    private function getRouteName(Request $request): ?string
    {
        $route = $request->route();

        if (!$route) {
            return null;
        }

        // Prefer named route
        $name = $route->getName();
        if ($name) {
            return $name;
        }

        // Fall back to URI pattern
        $uri = $route->uri();

        // Normalize the URI by replacing parameters with placeholders
        // e.g., users/123 becomes users/{id}
        return $uri;
    }
}
