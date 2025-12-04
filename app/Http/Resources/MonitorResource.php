<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonitorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'url' => $this->url,
            'host' => $this->host,
            'port' => $this->port,
            'status' => $this->status,
            'is_enabled' => $this->is_enabled,
            'check_interval' => $this->check_interval,
            'settings' => $this->settings,
            'uptime_percentage' => $this->uptime_percentage,
            'last_checked_at' => $this->last_checked_at?->toIso8601String(),
            'project' => $this->whenLoaded('project', fn () => [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ]),
            'latest_check' => $this->whenLoaded('latestCheck', fn () => [
                'is_successful' => $this->latestCheck->is_successful,
                'status_code' => $this->latestCheck->status_code,
                'response_time' => $this->latestCheck->response_time,
                'checked_at' => $this->latestCheck->checked_at->toIso8601String(),
            ]),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
