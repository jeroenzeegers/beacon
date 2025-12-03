<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EscalationPolicy extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'repeat_count',
        'is_active',
    ];

    protected $casts = [
        'repeat_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(EscalationRule::class)->orderBy('level');
    }

    public function monitors(): HasMany
    {
        return $this->hasMany(Monitor::class);
    }

    public function getTargetsForLevel(int $level): array
    {
        $rule = $this->rules()->where('level', $level)->first();

        if (! $rule) {
            return [];
        }

        return $rule->getTargets();
    }
}
