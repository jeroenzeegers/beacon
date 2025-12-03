<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Settings extends Component
{
    public bool $maintenanceMode = false;

    public string $appName = '';

    public string $appUrl = '';

    public bool $registrationEnabled = true;

    public function mount(): void
    {
        $this->maintenanceMode = app()->isDownForMaintenance();
        $this->appName = config('app.name');
        $this->appUrl = config('app.url');
        $this->registrationEnabled = Cache::get('registration_enabled', true);
    }

    public function render(): View
    {
        return view('livewire.admin.settings');
    }

    public function toggleMaintenanceMode(): void
    {
        if ($this->maintenanceMode) {
            Artisan::call('up');
            $this->maintenanceMode = false;
            AuditLog::log('maintenance', 'Disabled maintenance mode');
            session()->flash('success', 'Maintenance mode disabled.');
        } else {
            Artisan::call('down', [
                '--secret' => 'beacon-admin-'.now()->timestamp,
            ]);
            $this->maintenanceMode = true;
            AuditLog::log('maintenance', 'Enabled maintenance mode');
            session()->flash('success', 'Maintenance mode enabled. Use the secret link to bypass.');
        }
    }

    public function toggleRegistration(): void
    {
        $this->registrationEnabled = ! $this->registrationEnabled;
        Cache::forever('registration_enabled', $this->registrationEnabled);

        AuditLog::log(
            'settings',
            $this->registrationEnabled ? 'Enabled user registration' : 'Disabled user registration'
        );

        session()->flash('success', $this->registrationEnabled
            ? 'User registration enabled.'
            : 'User registration disabled.');
    }

    public function clearCache(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        AuditLog::log('cache', 'Cleared all caches');

        session()->flash('success', 'All caches cleared successfully.');
    }

    public function optimizeApplication(): void
    {
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        AuditLog::log('optimize', 'Optimized application');

        session()->flash('success', 'Application optimized successfully.');
    }
}
