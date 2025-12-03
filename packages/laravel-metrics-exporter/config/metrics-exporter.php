<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Metrics Collection
    |--------------------------------------------------------------------------
    |
    | This option controls whether the metrics exporter is enabled. When
    | disabled, no metrics will be collected and the endpoint will return
    | a 503 Service Unavailable response.
    |
    */
    'enabled' => env('METRICS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Metrics Endpoint Path
    |--------------------------------------------------------------------------
    |
    | The URL path where the metrics endpoint will be available. This should
    | be a path that is not used by your application. The default is /metrics.
    |
    */
    'path' => env('METRICS_PATH', '/metrics'),

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Configure how the metrics endpoint is protected. Available types:
    | - token: Bearer token authentication (recommended)
    | - basic: HTTP Basic authentication
    | - none: No authentication (use only for internal networks)
    |
    */
    'auth' => [
        'type' => env('METRICS_AUTH_TYPE', 'token'),
        'token' => env('METRICS_API_TOKEN'),
        'username' => env('METRICS_BASIC_USERNAME'),
        'password' => env('METRICS_BASIC_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Enabled Collectors
    |--------------------------------------------------------------------------
    |
    | Enable or disable individual metric collectors. Each collector gathers
    | specific types of metrics from your application.
    |
    */
    'collectors' => [
        'requests' => env('METRICS_COLLECT_REQUESTS', true),
        'system' => env('METRICS_COLLECT_SYSTEM', true),
        'database' => env('METRICS_COLLECT_DATABASE', true),
        'cache' => env('METRICS_COLLECT_CACHE', true),
        'queue' => env('METRICS_COLLECT_QUEUE', true),
        'errors' => env('METRICS_COLLECT_ERRORS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | The storage driver used to persist metrics between requests. Available
    | drivers: redis, file, array (for testing only).
    |
    */
    'storage' => [
        'driver' => env('METRICS_STORAGE', 'redis'),
        'prefix' => env('METRICS_PREFIX', 'beacon_metrics:'),

        // Redis-specific settings
        'redis' => [
            'connection' => env('METRICS_REDIS_CONNECTION', 'default'),
        ],

        // File-specific settings
        'file' => [
            'path' => env('METRICS_FILE_PATH', storage_path('framework/metrics')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Retention
    |--------------------------------------------------------------------------
    |
    | How long to retain metric data in minutes. Older data will be
    | automatically cleaned up. This affects per-minute calculations.
    |
    */
    'retention_minutes' => env('METRICS_RETENTION', 60),

    /*
    |--------------------------------------------------------------------------
    | Request Tracking
    |--------------------------------------------------------------------------
    |
    | Configure how HTTP requests are tracked for metrics collection.
    |
    */
    'requests' => [
        // Track per-route statistics
        'track_routes' => env('METRICS_TRACK_ROUTES', true),

        // Maximum number of routes to track (to prevent memory issues)
        'max_routes' => env('METRICS_MAX_ROUTES', 100),

        // Paths to ignore (glob patterns)
        'ignore_paths' => [
            'telescope*',
            'horizon*',
            '_debugbar*',
            'metrics',
            'health',
            'livewire/*',
        ],

        // Response time threshold for slow requests (milliseconds)
        'slow_threshold_ms' => env('METRICS_SLOW_THRESHOLD', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Tracking
    |--------------------------------------------------------------------------
    |
    | Configure how database queries are tracked. Note that enabling detailed
    | query tracking can have a performance impact in high-traffic applications.
    |
    */
    'database' => [
        // Threshold for slow query detection (milliseconds)
        'slow_query_threshold_ms' => env('METRICS_SLOW_QUERY_THRESHOLD', 100),

        // Track individual query patterns (can be expensive)
        'track_queries' => env('METRICS_TRACK_QUERIES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Tracking
    |--------------------------------------------------------------------------
    |
    | Configure which queues to monitor for pending/failed job counts.
    |
    */
    'queue' => [
        // Queue names to monitor (null = all queues)
        'queues' => env('METRICS_QUEUE_NAMES') ? explode(',', env('METRICS_QUEUE_NAMES')) : ['default'],
    ],
];
