<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_report_id',
        'status',
        'recipients_count',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'recipients_count' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function scheduledReport(): BelongsTo
    {
        return $this->belongsTo(ScheduledReport::class);
    }
}
