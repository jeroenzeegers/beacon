<?php

use App\Http\Controllers\StatusPageController;
use App\Livewire\Admin\Analytics as AdminAnalytics;
use App\Livewire\Admin\AuditLogs;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Plans as AdminPlans;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Admin\Subscriptions as AdminSubscriptions;
use App\Livewire\Admin\SystemHealth;
use App\Livewire\Admin\Teams as AdminTeams;
use App\Livewire\Admin\Users as AdminUsers;
use App\Livewire\Billing\Dashboard as BillingDashboard;
use App\Livewire\Billing\Plans;
use App\Livewire\Dashboard;
use App\Livewire\LiveStatus;
use App\Livewire\Monitors\Create as MonitorCreate;
use App\Livewire\Monitors\Index as MonitorIndex;
use App\Livewire\Monitors\Show as MonitorShow;
use App\Livewire\Projects\Create as ProjectCreate;
use App\Livewire\Projects\Index as ProjectIndex;
use App\Livewire\Projects\Show as ProjectShow;
use App\Livewire\Teams\Members;
use App\Livewire\Teams\Settings;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Broadcasting authentication routes
Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::view('/', 'welcome');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/live-status', LiveStatus::class)->name('live-status');

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

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/users', AdminUsers::class)->name('users');
    Route::get('/teams', AdminTeams::class)->name('teams');
    Route::get('/subscriptions', AdminSubscriptions::class)->name('subscriptions');
    Route::get('/plans', AdminPlans::class)->name('plans');
    Route::get('/settings', AdminSettings::class)->name('settings');
    Route::get('/system-health', SystemHealth::class)->name('system-health');
    Route::get('/analytics', AdminAnalytics::class)->name('analytics');
    Route::get('/audit-logs', AuditLogs::class)->name('audit-logs');
});

require __DIR__.'/auth.php';
