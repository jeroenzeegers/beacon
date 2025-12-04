<?php

declare(strict_types=1);

namespace Beacon\MetricsExporter\Http\Controllers;

use Beacon\MetricsExporter\MetricsManager;
use Illuminate\Http\JsonResponse;

class MetricsController
{
    public function __construct(
        private MetricsManager $manager
    ) {}

    public function __invoke(): JsonResponse
    {
        if (! config('metrics-exporter.enabled', true)) {
            return response()->json([
                'error' => 'Service Unavailable',
                'message' => 'Metrics collection is disabled.',
            ], 503);
        }

        $metrics = $this->manager->collect();

        return response()->json($metrics);
    }
}
