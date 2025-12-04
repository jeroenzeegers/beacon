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
use App\Livewire\Alerts\Channels\Create as AlertChannelCreate;
use App\Livewire\Alerts\Channels\Index as AlertChannelIndex;
use App\Livewire\Alerts\Logs\Index as AlertLogIndex;
use App\Livewire\Alerts\Rules\Create as AlertRuleCreate;
use App\Livewire\Alerts\Rules\Index as AlertRuleIndex;
use App\Livewire\Billing\Dashboard as BillingDashboard;
use App\Livewire\Billing\Plans;
use App\Livewire\Dashboard;
use App\Livewire\Heartbeats\Create as HeartbeatCreate;
use App\Livewire\Heartbeats\Index as HeartbeatIndex;
use App\Livewire\Heartbeats\Show as HeartbeatShow;
use App\Livewire\Incidents\Create as IncidentCreate;
use App\Livewire\Incidents\Index as IncidentIndex;
use App\Livewire\Incidents\Show as IncidentShow;
use App\Livewire\LiveStatus;
use App\Livewire\Maintenance\Create as MaintenanceCreate;
use App\Livewire\Maintenance\Index as MaintenanceIndex;
use App\Livewire\Monitors\Create as MonitorCreate;
use App\Livewire\Monitors\Index as MonitorIndex;
use App\Livewire\Monitors\Show as MonitorShow;
use App\Livewire\Projects\Create as ProjectCreate;
use App\Livewire\Projects\Index as ProjectIndex;
use App\Livewire\Projects\Show as ProjectShow;
use App\Livewire\Reports\Create as ReportCreate;
use App\Livewire\Reports\Index as ReportIndex;
use App\Livewire\StatusPages\Create as StatusPageCreate;
use App\Livewire\StatusPages\Index as StatusPageIndex;
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

    // Heartbeats (Cron Monitoring)
    Route::get('/heartbeats', HeartbeatIndex::class)->name('heartbeats.index');
    Route::get('/heartbeats/create', HeartbeatCreate::class)->name('heartbeats.create');
    Route::get('/heartbeats/{id}', HeartbeatShow::class)->name('heartbeats.show');
    Route::get('/heartbeats/{id}/edit', HeartbeatCreate::class)->name('heartbeats.edit');

    // Maintenance Windows
    Route::get('/maintenance', MaintenanceIndex::class)->name('maintenance.index');
    Route::get('/maintenance/create', MaintenanceCreate::class)->name('maintenance.create');
    Route::get('/maintenance/{id}/edit', MaintenanceCreate::class)->name('maintenance.edit');

    // Scheduled Reports
    Route::get('/reports', ReportIndex::class)->name('reports.index');
    Route::get('/reports/create', ReportCreate::class)->name('reports.create');
    Route::get('/reports/{id}/edit', ReportCreate::class)->name('reports.edit');

    // Alert Channels
    Route::get('/alerts/channels', AlertChannelIndex::class)->name('alerts.channels.index');
    Route::get('/alerts/channels/create', AlertChannelCreate::class)->name('alerts.channels.create');
    Route::get('/alerts/channels/{id}/edit', AlertChannelCreate::class)->name('alerts.channels.edit');

    // Alert Rules
    Route::get('/alerts/rules', AlertRuleIndex::class)->name('alerts.rules.index');
    Route::get('/alerts/rules/create', AlertRuleCreate::class)->name('alerts.rules.create');
    Route::get('/alerts/rules/{id}/edit', AlertRuleCreate::class)->name('alerts.rules.edit');

    // Alert Logs
    Route::get('/alerts/logs', AlertLogIndex::class)->name('alerts.logs.index');

    // Status Pages
    Route::get('/status-pages', StatusPageIndex::class)->name('status-pages.index');
    Route::get('/status-pages/create', StatusPageCreate::class)->name('status-pages.create');
    Route::get('/status-pages/{id}/edit', StatusPageCreate::class)->name('status-pages.edit');

    // Incidents
    Route::get('/incidents', IncidentIndex::class)->name('incidents.index');
    Route::get('/incidents/create', IncidentCreate::class)->name('incidents.create');
    Route::get('/incidents/{id}', IncidentShow::class)->name('incidents.show');
    Route::get('/incidents/{id}/edit', IncidentCreate::class)->name('incidents.edit');

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
