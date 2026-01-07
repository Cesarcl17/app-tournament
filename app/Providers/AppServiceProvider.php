<?php

namespace App\Providers;

use App\Models\TournamentMatch;
use App\Observers\TournamentMatchObserver;
use App\Services\StatisticsService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register StatisticsService as singleton
        $this->app->singleton(StatisticsService::class, function ($app) {
            return new StatisticsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        TournamentMatch::observe(TournamentMatchObserver::class);

        // Usar template de paginación simple
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.simple-default');
    }
}
