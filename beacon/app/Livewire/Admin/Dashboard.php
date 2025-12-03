<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Monitor;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    /**
     * Get the listeners for the component.
     */
    public function getListeners(): array
    {
        return [
            'echo-private:admin.stats,user.registered' => 'handleNewUser',
            'echo-private:admin.stats,stats.updated' => 'handleStatsUpdate',
        ];
    }

    /**
     * Handle new user registered event.
     */
    public function handleNewUser(array $data): void
    {
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => "New user registered: {$data['user']['name']}",
        ]);
    }

    /**
     * Handle stats update event.
     */
    public function handleStatsUpdate(array $data): void
    {
        // Component will re-render with fresh data
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->getStats(),
            'recentUsers' => $this->getRecentUsers(),
            'recentActivity' => $this->getRecentActivity(),
            'systemHealth' => $this->getSystemHealth(),
        ]);
    }

    private function getStats(): array
    {
        return [
            'total_users' => User::count(),
            'users_today' => User::whereDate('created_at', today())->count(),
            'users_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            'total_teams' => Team::count(),
            'total_monitors' => Monitor::count(),
            'active_monitors' => Monitor::where('is_active', true)->count(),
            'subscriptions_active' => Team::whereHas('subscriptions', function ($q) {
                $q->where('stripe_status', 'active');
            })->count(),
        ];
    }

    private function getRecentUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::latest()
            ->take(5)
            ->get();
    }

    private function getRecentActivity(): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get();
    }

    private function getSystemHealth(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
        ];
    }

    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $time = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'healthy',
                'message' => "Response time: {$time}ms",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health_check_'.time();
            cache()->put($key, true, 10);
            $result = cache()->get($key);
            cache()->forget($key);

            return [
                'status' => $result ? 'healthy' : 'error',
                'message' => $result ? 'Cache is working' : 'Cache read failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    private function checkQueue(): array
    {
        try {
            $failedJobs = DB::table('failed_jobs')->count();

            return [
                'status' => $failedJobs > 10 ? 'warning' : 'healthy',
                'message' => "Failed jobs: {$failedJobs}",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'message' => 'Could not check queue status',
            ];
        }
    }
}
