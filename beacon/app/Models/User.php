<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'current_team_id',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the current team the user is working in.
     */
    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Get all teams the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get teams owned by the user.
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    /**
     * Check if user belongs to a team.
     */
    public function belongsToTeam(Team $team): bool
    {
        return $this->teams()->where('team_id', $team->id)->exists();
    }

    /**
     * Get the user's role in a specific team.
     */
    public function roleInTeam(Team $team): ?string
    {
        return $this->teams()
            ->where('team_id', $team->id)
            ->first()
            ?->pivot
            ?->role;
    }

    /**
     * Switch to a different team.
     */
    public function switchToTeam(Team $team): bool
    {
        if (!$this->belongsToTeam($team)) {
            return false;
        }

        $this->update(['current_team_id' => $team->id]);

        return true;
    }

    /**
     * Create a new team for this user.
     */
    public function createTeam(string $name): Team
    {
        $team = Team::create([
            'name' => $name,
            'owner_id' => $this->id,
        ]);

        // Add user as owner
        $team->addUser($this, 'owner');

        // Set as current team if user doesn't have one
        if (!$this->current_team_id) {
            $this->update(['current_team_id' => $team->id]);
        }

        return $team;
    }

    /**
     * Check if user is owner of a team.
     */
    public function isOwnerOfTeam(Team $team): bool
    {
        return $team->owner_id === $this->id;
    }

    /**
     * Check if user is admin of a team.
     */
    public function isAdminOfTeam(Team $team): bool
    {
        $role = $this->roleInTeam($team);

        return in_array($role, ['owner', 'admin'], true);
    }

    /**
     * Check if user can access a team.
     */
    public function canAccessTeam(Team $team): bool
    {
        return $this->belongsToTeam($team);
    }
}
