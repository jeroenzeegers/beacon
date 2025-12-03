<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetricSnapshot extends Model
{
    use HasFactory;

    public const PERIOD_MINUTE = 'minute';
    public const PERIOD_HOUR = 'hour';
    public const PERIOD_DAY = 'day';

    public const TYPE_REQUEST_COUNT = 'request_count';
    public const TYPE_ERROR_RATE = 'error_rate';
    public const TYPE_RESPONSE_TIME_AVG = 'response_time_avg';
    public const TYPE_RESPONSE_TIME_P95 = 'response_time_p95';
    public const TYPE_RESPONSE_TIME_P99 = 'response_time_p99';
    public const TYPE_THROUGHPUT = 'throughput';
    public const TYPE_ERROR_COUNT = 'error_count';
    public const TYPE_CPU_USAGE = 'cpu_usage';
    public const TYPE_MEMORY_USAGE = 'memory_usage';

    protected $fillable = [
        'project_id',
        'metric_type',
        'value',
        'dimensions',
        'aggregation_period',
        'recorded_at',
    ];

    protected $casts = [
        'value' => 'decimal:6',
        'dimensions' => 'array',
        'recorded_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('metric_type', $type);
    }

    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeForPeriod(Builder $query, string $period): Builder
    {
        return $query->where('aggregation_period', $period);
    }

    public function scopeBetween(Builder $query, $start, $end): Builder
    {
        return $query->whereBetween('recorded_at', [$start, $end]);
    }

    public function scopeLastHours(Builder $query, int $hours): Builder
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }

    public function scopeLastDays(Builder $query, int $days): Builder
    {
        return $query->where('recorded_at', '>=', now()->subDays($days));
    }

    public static function record(
        int $projectId,
        string $metricType,
        float $value,
        ?array $dimensions = null,
        string $period = self::PERIOD_MINUTE,
        ?\DateTimeInterface $recordedAt = null
    ): self {
        return self::create([
            'project_id' => $projectId,
            'metric_type' => $metricType,
            'value' => $value,
            'dimensions' => $dimensions,
            'aggregation_period' => $period,
            'recorded_at' => $recordedAt ?? now(),
        ]);
    }

    public static function getAverageForPeriod(
        int $projectId,
        string $metricType,
        $start,
        $end,
        string $period = self::PERIOD_MINUTE
    ): ?float {
        $avg = self::forProject($projectId)
            ->ofType($metricType)
            ->forPeriod($period)
            ->between($start, $end)
            ->avg('value');

        return $avg ? (float) $avg : null;
    }

    public static function getSumForPeriod(
        int $projectId,
        string $metricType,
        $start,
        $end,
        string $period = self::PERIOD_MINUTE
    ): float {
        return (float) self::forProject($projectId)
            ->ofType($metricType)
            ->forPeriod($period)
            ->between($start, $end)
            ->sum('value');
    }

    public static function getAvailableMetricTypes(): array
    {
        return [
            self::TYPE_REQUEST_COUNT => 'Request Count',
            self::TYPE_ERROR_RATE => 'Error Rate',
            self::TYPE_RESPONSE_TIME_AVG => 'Avg Response Time',
            self::TYPE_RESPONSE_TIME_P95 => 'P95 Response Time',
            self::TYPE_RESPONSE_TIME_P99 => 'P99 Response Time',
            self::TYPE_THROUGHPUT => 'Throughput',
            self::TYPE_ERROR_COUNT => 'Error Count',
            self::TYPE_CPU_USAGE => 'CPU Usage',
            self::TYPE_MEMORY_USAGE => 'Memory Usage',
        ];
    }
}
