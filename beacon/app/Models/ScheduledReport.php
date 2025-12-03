<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduledReport extends Model
{
    use BelongsToTeam, HasFactory;

    public const TYPE_DAILY_SUMMARY = 'daily_summary';
    public const TYPE_WEEKLY_SUMMARY = 'weekly_summary';
    public const TYPE_MONTHLY_SUMMARY = 'monthly_summary';
    public const TYPE_SLA_REPORT = 'sla_report';

    public const FREQUENCY_DAILY = 'daily';
    public const FREQUENCY_WEEKLY = 'weekly';
    public const FREQUENCY_MONTHLY = 'monthly';

    protected $fillable = [
        'team_id',
        'name',
        'type',
        'frequency',
        'day_of_week',
        'day_of_month',
        'time',
        'timezone',
        'recipients',
        'config',
        'is_active',
        'last_sent_at',
        'next_send_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
        'next_send_at' => 'datetime',
        'day_of_month' => 'integer',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReportLog::class);
    }

    public function calculateNextSendAt(): void
    {
        $now = now($this->timezone);
        $time = explode(':', $this->time);
        $hour = (int) $time[0];
        $minute = (int) ($time[1] ?? 0);

        $next = match ($this->frequency) {
            self::FREQUENCY_DAILY => $now->copy()->setTime($hour, $minute)->addDay(),
            self::FREQUENCY_WEEKLY => $now->copy()->next($this->day_of_week)->setTime($hour, $minute),
            self::FREQUENCY_MONTHLY => $now->copy()->setDay($this->day_of_month)->setTime($hour, $minute)->addMonth(),
            default => $now->copy()->addDay(),
        };

        if ($next->lte($now)) {
            $next = match ($this->frequency) {
                self::FREQUENCY_DAILY => $next->addDay(),
                self::FREQUENCY_WEEKLY => $next->addWeek(),
                self::FREQUENCY_MONTHLY => $next->addMonth(),
                default => $next->addDay(),
            };
        }

        $this->update(['next_send_at' => $next]);
    }

    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_DAILY_SUMMARY => 'Daily Summary',
            self::TYPE_WEEKLY_SUMMARY => 'Weekly Summary',
            self::TYPE_MONTHLY_SUMMARY => 'Monthly Summary',
            self::TYPE_SLA_REPORT => 'SLA Report',
        ];
    }

    public static function getAvailableFrequencies(): array
    {
        return [
            self::FREQUENCY_DAILY => 'Daily',
            self::FREQUENCY_WEEKLY => 'Weekly',
            self::FREQUENCY_MONTHLY => 'Monthly',
        ];
    }
}
