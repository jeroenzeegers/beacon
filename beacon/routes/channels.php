<?php

use App\Models\Team;
use Illuminate\Support\Facades\Broadcast;

// Private user channel
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Team channel - for monitor updates, alerts, etc.
Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    return $user->teams()->where('team_id', $teamId)->exists();
});

// Monitor-specific channel
Broadcast::channel('monitor.{monitorId}', function ($user, $monitorId) {
    $monitor = \App\Models\Monitor::find($monitorId);
    return $monitor && $user->teams()->where('team_id', $monitor->team_id)->exists();
});

// Admin channel - only for admin users
Broadcast::channel('admin', function ($user) {
    return $user->is_admin;
});

// Admin stats channel for real-time dashboard updates
Broadcast::channel('admin.stats', function ($user) {
    return $user->is_admin;
});
