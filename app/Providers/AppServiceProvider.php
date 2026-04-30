<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Login: 10 attempts/min per IP — blocks brute force on all portals
        RateLimiter::for('login', fn ($request) =>
            Limit::perMinute(10)->by($request->ip())
        );

        // Password reset: 3/min per IP — prevents reset-link flooding
        RateLimiter::for('password-reset', fn ($request) =>
            Limit::perMinute(3)->by($request->ip())
        );

        // Super admin panel: 300 requests/min per user
        RateLimiter::for('admin', fn ($request) =>
            Limit::perMinute(300)->by(optional($request->user())->id ?: $request->ip())
        );

        // Tenant portal (employees + client): 300 requests/min per user
        RateLimiter::for('tenant', fn ($request) =>
            Limit::perMinute(300)->by(optional($request->user())->id ?: $request->ip())
        );
    }
}
