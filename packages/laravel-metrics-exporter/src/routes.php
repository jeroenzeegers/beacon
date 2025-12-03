<?php

use Beacon\MetricsExporter\Http\Controllers\MetricsController;
use Beacon\MetricsExporter\Http\Middleware\MetricsAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::get(config('metrics-exporter.path', '/metrics'), MetricsController::class)
    ->middleware(MetricsAuthMiddleware::class)
    ->name('metrics.index');
