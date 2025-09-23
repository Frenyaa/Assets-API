<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind custom services or singletons here
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix key length issue for older MySQL
        Schema::defaultStringLength(191);

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Use Bootstrap pagination
        Paginator::useBootstrapFive();
    }
}