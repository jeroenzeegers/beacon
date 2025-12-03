<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTeam
{
    protected static function bootBelongsToTeam(): void
    {
        // Add global scope to filter by current team
        static::addGlobalScope('team', function (Builder $builder) {
            if (auth()->check() && auth()->user()->current_team_id) {
                $builder->where($builder->getModel()->getTable() . '.team_id', auth()->user()->current_team_id);
            }
        });

        // Auto-assign team_id when creating
        static::creating(function (Model $model) {
            if (auth()->check() && empty($model->team_id)) {
                $model->team_id = auth()->user()->current_team_id;
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Query without the team scope.
     */
    public function scopeWithoutTeamScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('team');
    }

    /**
     * Query for a specific team.
     */
    public function scopeForTeam(Builder $query, Team|int $team): Builder
    {
        $teamId = $team instanceof Team ? $team->id : $team;

        return $query->withoutGlobalScope('team')->where('team_id', $teamId);
    }
}
