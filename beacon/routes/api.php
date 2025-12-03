<?php

use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\MonitorController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\HeartbeatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// API v1 routes
Route::prefix('v1')->middleware(['auth:sanctum', 'api.rate-limit'])->group(function () {
    // Team
    Route::get('/team', [TeamController::class, 'show']);

    // Projects
    Route::apiResource('projects', ProjectController::class);

    // Monitors
    Route::apiResource('monitors', MonitorController::class);
    Route::post('/monitors/{monitor}/check', [MonitorController::class, 'triggerCheck']);
    Route::get('/monitors/{monitor}/checks', [MonitorController::class, 'checks']);
    Route::get('/monitors/{monitor}/stats', [MonitorController::class, 'stats']);

    // Incidents
    Route::apiResource('incidents', IncidentController::class);
    Route::post('/incidents/{incident}/updates', [IncidentController::class, 'addUpdate']);
    Route::post('/incidents/{incident}/resolve', [IncidentController::class, 'resolve']);
});

// Public heartbeat endpoints (no auth required)
Route::any('/heartbeat/{slug}', [HeartbeatController::class, 'ping'])->name('heartbeat.ping');
Route::any('/heartbeat/{slug}/fail', [HeartbeatController::class, 'fail'])->name('heartbeat.fail');

// Public badge endpoints (no auth required)
Route::get('/badge/{monitorId}/uptime', [BadgeController::class, 'uptime'])->name('badge.uptime');
Route::get('/badge/{monitorId}/status', [BadgeController::class, 'status'])->name('badge.status');
Route::get('/badge/{monitorId}/response-time', [BadgeController::class, 'responseTime'])->name('badge.response-time');
