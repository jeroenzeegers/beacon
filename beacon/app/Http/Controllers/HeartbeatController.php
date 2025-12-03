<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Heartbeat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeartbeatController extends Controller
{
    public function ping(Request $request, string $slug): JsonResponse
    {
        $heartbeat = Heartbeat::where('slug', $slug)->first();

        if (!$heartbeat) {
            return response()->json(['error' => 'Heartbeat not found'], 404);
        }

        if (!$heartbeat->is_active) {
            return response()->json(['error' => 'Heartbeat is inactive'], 400);
        }

        $status = $request->input('status', 'success');
        if (!in_array($status, ['success', 'fail'])) {
            $status = 'success';
        }

        $ping = $heartbeat->recordPing([
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $request->except(['status']),
        ]);

        return response()->json([
            'status' => 'ok',
            'heartbeat' => $heartbeat->name,
            'ping_id' => $ping->id,
            'next_expected_at' => $heartbeat->next_expected_at->toIso8601String(),
        ]);
    }

    public function fail(Request $request, string $slug): JsonResponse
    {
        $heartbeat = Heartbeat::where('slug', $slug)->first();

        if (!$heartbeat) {
            return response()->json(['error' => 'Heartbeat not found'], 404);
        }

        if (!$heartbeat->is_active) {
            return response()->json(['error' => 'Heartbeat is inactive'], 400);
        }

        $ping = $heartbeat->recordPing([
            'status' => 'fail',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $request->all(),
        ]);

        return response()->json([
            'status' => 'recorded',
            'heartbeat' => $heartbeat->name,
            'ping_id' => $ping->id,
            'ping_status' => 'fail',
        ]);
    }
}
