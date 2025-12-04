<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'severity' => $this->severity,
            'started_at' => $this->started_at->toIso8601String(),
            'resolved_at' => $this->resolved_at?->toIso8601String(),
            'monitor' => $this->whenLoaded('monitor', fn () => [
                'id' => $this->monitor->id,
                'name' => $this->monitor->name,
            ]),
            'updates' => $this->whenLoaded('updates', fn () => $this->updates->map(fn ($update) => [
                'id' => $update->id,
                'status' => $update->status,
                'message' => $update->message,
                'created_at' => $update->created_at->toIso8601String(),
            ])),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
