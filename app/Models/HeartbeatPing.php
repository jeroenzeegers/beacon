<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeartbeatPing extends Model
{
    use HasFactory;

    protected $fillable = [
        'heartbeat_id',
        'status',
        'ip_address',
        'user_agent',
        'payload',
        'pinged_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'pinged_at' => 'datetime',
    ];

    public function heartbeat(): BelongsTo
    {
        return $this->belongsTo(Heartbeat::class);
    }
}
