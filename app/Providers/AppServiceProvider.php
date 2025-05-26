<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
        // Rate limiting untuk form submission
        RateLimiter::for('event-submissions', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiting untuk upload file
        RateLimiter::for('file-uploads', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // Cache configuration
        if (!app()->isLocal()) {
            config(['cache.default' => 'redis']);
            config(['session.driver' => 'redis']);
        }

        // Register middleware alias
        Route::aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);
    }
}
