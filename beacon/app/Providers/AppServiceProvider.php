<?php

namespace App\Providers;

use App\Models\Team;
use App\Support\AlertSenders\AlertSenderFactory;
use App\Support\Checkers\CheckerFactory;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CheckerFactory::class, function () {
            return new CheckerFactory;
        });

        $this->app->singleton(AlertSenderFactory::class, function () {
            return new AlertSenderFactory;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Cashier to use Team model for billing
        Cashier::useCustomerModel(Team::class);
    }
}
