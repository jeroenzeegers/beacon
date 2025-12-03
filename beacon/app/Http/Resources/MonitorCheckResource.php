<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonitorCheckResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_successful' => $this->is_successful,
            'status_code' => $this->status_code,
            'response_time' => $this->response_time,
            'error_message' => $this->error_message,
            'response_headers' => $this->response_headers,
            'checked_at' => $this->checked_at->toIso8601String(),
        ];
    }
}
