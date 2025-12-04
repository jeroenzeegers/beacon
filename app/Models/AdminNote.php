<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AdminNote extends Model
{
    protected $fillable = [
        'admin_id',
        'notable_type',
        'notable_id',
        'content',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    /**
     * Get the admin who created the note.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the notable model.
     */
    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to get pinned notes first.
     */
    public function scopePinnedFirst($query)
    {
        return $query->orderByDesc('is_pinned')->latest();
    }

    /**
     * Add a note to a model.
     */
    public static function addNote(Model $model, string $content, bool $isPinned = false): self
    {
        return self::create([
            'admin_id' => auth()->id(),
            'notable_type' => get_class($model),
            'notable_id' => $model->id,
            'content' => $content,
            'is_pinned' => $isPinned,
        ]);
    }
}
