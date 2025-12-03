<?php

use App\Http\Controllers\StatusPageController;
use App\Livewire\Billing\Dashboard as BillingDashboard;
use App\Livewire\Billing\Plans;
use App\Livewire\Dashboard;
use App\Livewire\Monitors\Create as MonitorCreate;
use App\Livewire\Monitors\Index as MonitorIndex;
use App\Livewire\Monitors\Show as MonitorShow;
use App\Livewire\Projects\Create as ProjectCreate;
use App\Livewire\Projects\Index as ProjectIndex;
use App\Livewire\Projects\Show as ProjectShow;
use App\Livewire\Teams\Members;
use App\Livewire\Teams\Settings;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Monitors
    Route::get('/monitors', MonitorIndex::class)->name('monitors.index');
    Route::get('/monitors/create', MonitorCreate::class)->name('monitors.create');
    Route::get('/monitors/{id}', MonitorShow::class)->name('monitors.show');
    Route::get('/monitors/{id}/edit', MonitorCreate::class)->name('monitors.edit');

    // Projects
    Route::get('/projects', ProjectIndex::class)->name('projects.index');
    Route::get('/projects/create', ProjectCreate::class)->name('projects.create');
    Route::get('/projects/{id}', ProjectShow::class)->name('projects.show');
    Route::get('/projects/{id}/edit', ProjectCreate::class)->name('projects.edit');

    // Team
    Route::get('/team/settings', Settings::class)->name('team.settings');
    Route::get('/team/members', Members::class)->name('team.members');

    // Billing
    Route::get('/billing', BillingDashboard::class)->name('billing.dashboard');
    Route::get('/billing/plans', Plans::class)->name('billing.plans');
});

// Public status pages
Route::get('/status/{slug}', [StatusPageController::class, 'show'])->name('status-page.show');
Route::get('/status/{slug}/api', [StatusPageController::class, 'apiStatus'])->name('status-page.api');

require __DIR__.'/auth.php';
