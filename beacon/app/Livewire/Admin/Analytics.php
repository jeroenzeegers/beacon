<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Monitor;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Analytics extends Component
{
    public string $period = '30';

    public function render(): View
    {
        return view('livewire.admin.analytics', [
            'userStats' => $this->getUserStats(),
            'growthStats' => $this->getGrowthStats(),
            'usageStats' => $this->getUsageStats(),
            'topTeams' => $this->getTopTeams(),
        ]);
    }

    private function getUserStats(): array
    {
        $days = (int) $this->period;

        return [
            'new_users' => User::where('created_at', '>=', now()->subDays($days))->count(),
            'active_users' => User::where('last_login_at', '>=', now()->subDays($days))->count(),
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
        ];
    }

    private function getGrowthStats(): array
    {
        $days = (int) $this->period;

        // Get daily user signups
        $dailySignups = User::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Get daily team creations
        $dailyTeams = Team::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return [
            'daily_signups' => $dailySignups,
            'daily_teams' => $dailyTeams,
        ];
    }

    private function getUsageStats(): array
    {
        return [
            'total_monitors' => Monitor::count(),
            'active_monitors' => Monitor::where('is_active', true)->count(),
            'total_teams' => Team::count(),
            'checks_today' => DB::table('monitor_checks')
                ->whereDate('created_at', today())
                ->count(),
        ];
    }

    private function getTopTeams(): \Illuminate\Support\Collection
    {
        return Team::withCount('monitors')
            ->orderByDesc('monitors_count')
            ->take(10)
            ->get();
    }
}
