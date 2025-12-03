# Laravel Metrics Exporter

A lightweight Laravel package that exposes application metrics via a JSON endpoint for external monitoring platforms.

## Features

- **Request Metrics**: Track request counts, response times, status codes, and per-route statistics
- **System Metrics**: Monitor PHP version, memory usage, CPU load, disk space, and OPcache status
- **Database Metrics**: Track query counts, slow queries, and average query times
- **Cache Metrics**: Monitor cache hit/miss ratios
- **Queue Metrics**: Track pending and failed jobs
- **Error Metrics**: Count errors by severity level
- **Custom Metrics**: Add your own gauges, counters, and timings
- **Multiple Storage Drivers**: Redis (recommended), File, or Array (for testing)
- **Authentication**: Token-based or Basic auth

## Requirements

- PHP 8.2+
- Laravel 10.x or 11.x
- Redis (recommended) or filesystem access

## Installation

Install via Composer:

```bash
composer require beacon/laravel-metrics-exporter
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=metrics-exporter-config
```

## Configuration

Add these environment variables to your `.env` file:

```env
# Enable/disable metrics collection
METRICS_ENABLED=true

# Endpoint path (default: /metrics)
METRICS_PATH=/metrics

# Authentication (recommended: token)
METRICS_AUTH_TYPE=token
METRICS_API_TOKEN=your-secret-token-here

# Storage driver (redis recommended for production)
METRICS_STORAGE=redis

# Data retention in minutes
METRICS_RETENTION=60
```

### Authentication Options

**Token Authentication (Recommended)**
```env
METRICS_AUTH_TYPE=token
METRICS_API_TOKEN=your-secret-token-here
```

Access with: `Authorization: Bearer your-secret-token-here`

**Basic Authentication**
```env
METRICS_AUTH_TYPE=basic
METRICS_BASIC_USERNAME=metrics
METRICS_BASIC_PASSWORD=secret
```

**No Authentication** (use only in trusted networks)
```env
METRICS_AUTH_TYPE=none
```

## Usage

### Accessing Metrics

Once installed, metrics are available at your configured endpoint:

```bash
curl -H "Authorization: Bearer your-token" https://your-app.com/metrics
```

### Response Format

```json
{
  "collected_at": "2025-01-15T10:30:00+00:00",
  "app": {
    "name": "My App",
    "environment": "production",
    "debug": false
  },
  "requests": {
    "total": 15420,
    "per_minute": 125,
    "by_status": { "2xx": 14800, "3xx": 200, "4xx": 380, "5xx": 40 },
    "avg_response_time_ms": 145.5,
    "slow_requests": 23,
    "by_route": { "api.users.index": 500, "api.orders.store": 320 }
  },
  "system": {
    "php_version": "8.2.15",
    "laravel_version": "11.0.0",
    "memory_usage_mb": 64.5,
    "memory_peak_mb": 128.2,
    "memory_limit_mb": 512,
    "cpu_load": [1.2, 0.8, 0.5],
    "disk_free_gb": 45.2,
    "disk_total_gb": 100.0,
    "opcache_enabled": true
  },
  "database": {
    "queries_total": 45230,
    "queries_per_minute": 380,
    "slow_queries": 12,
    "avg_query_time_ms": 2.3,
    "connection_name": "pgsql"
  },
  "cache": {
    "hits": 8500,
    "misses": 1200,
    "hit_ratio": 0.876,
    "driver": "redis"
  },
  "queue": {
    "driver": "redis",
    "pending": 45,
    "failed": 3,
    "queues": { "default": { "pending": 45 } }
  },
  "errors": {
    "total": 23,
    "per_minute": 2,
    "by_level": { "error": 20, "critical": 2, "alert": 0, "emergency": 1 }
  }
}
```

### Custom Metrics

Use the `Metrics` facade to track custom application metrics:

```php
use Beacon\MetricsExporter\Facades\Metrics;

// Set a gauge (current value)
Metrics::gauge('active_users', User::where('last_seen', '>', now()->subMinutes(5))->count());
Metrics::gauge('queue_depth', Queue::size());

// Increment a counter
Metrics::increment('orders_placed');
Metrics::increment('api_calls', 5);

// Record a timing
Metrics::timing('payment_processing', 234.5); // milliseconds

// Measure a callback
$result = Metrics::measure('external_api_call', function () {
    return Http::get('https://api.example.com/data');
});
```

Custom metrics appear in the response under the `custom` key:

```json
{
  "custom": {
    "gauges": { "active_users": 142, "queue_depth": 45 },
    "counters": { "orders_placed": 567, "api_calls": 1234 }
  }
}
```

### Custom Collectors

Create your own collector by implementing `CollectorInterface`:

```php
use Beacon\MetricsExporter\Collectors\CollectorInterface;

class PaymentCollector implements CollectorInterface
{
    public function name(): string
    {
        return 'payments';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function collect(): array
    {
        return [
            'total_today' => Payment::whereDate('created_at', today())->sum('amount'),
            'count_today' => Payment::whereDate('created_at', today())->count(),
            'failed_today' => Payment::whereDate('created_at', today())->where('status', 'failed')->count(),
        ];
    }
}
```

Register in a service provider:

```php
use Beacon\MetricsExporter\Facades\Metrics;

public function boot()
{
    Metrics::registerCollector(new PaymentCollector());
}
```

## Storage Drivers

### Redis (Recommended)

Best for production. Provides atomic operations and automatic TTL-based cleanup.

```env
METRICS_STORAGE=redis
METRICS_REDIS_CONNECTION=default
```

### File

Fallback for environments without Redis. Uses file locking for concurrency.

```env
METRICS_STORAGE=file
METRICS_FILE_PATH=/path/to/storage/metrics
```

### Array

In-memory storage for testing. Data is not persisted between requests.

```env
METRICS_STORAGE=array
```

## Configuration Reference

See `config/metrics-exporter.php` for all available options:

| Option | Default | Description |
|--------|---------|-------------|
| `enabled` | `true` | Enable/disable metrics collection |
| `path` | `/metrics` | Endpoint URL path |
| `auth.type` | `token` | Authentication type: token, basic, none |
| `storage.driver` | `redis` | Storage driver: redis, file, array |
| `retention_minutes` | `60` | How long to retain metric data |
| `requests.slow_threshold_ms` | `1000` | Threshold for slow request detection |
| `database.slow_query_threshold_ms` | `100` | Threshold for slow query detection |

## Security Considerations

1. **Always use authentication** in production
2. **Use HTTPS** to protect the API token
3. **Limit access** via firewall rules if possible
4. **Rotate tokens** periodically
5. **Don't expose** sensitive data in custom metrics

## Integration with Beacon

This package is designed to work with the [Beacon Monitoring Platform](https://beacon.dev). Configure your project in Beacon with:

- **Metrics Endpoint**: `https://your-app.com/metrics`
- **API Token**: Your `METRICS_API_TOKEN` value

Beacon will poll this endpoint and store historical metrics for dashboards and alerting.

## License

MIT License. See [LICENSE](LICENSE) for details.
