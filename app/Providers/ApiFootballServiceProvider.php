<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ApiFootballServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('ApiFootball', function () {
            return new \App\Services\ApiFootballService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
