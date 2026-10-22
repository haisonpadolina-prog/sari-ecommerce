<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Application routes are registered from routes/web.php.
        // Keeping route loading out of this provider prevents the entire
        // application from failing to boot when an optional route file is
        // missing during a partial deployment.
    }
}
