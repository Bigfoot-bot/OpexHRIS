<?php

namespace App\Providers;

use App\Models\BrandingSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->validateBootConfiguration();
        $this->configureRateLimiting();

        // Share branding with all central admin views so layout reflects saved settings
        View::composer('central.*', function ($view) {
            try {
                $view->with('branding', BrandingSetting::first());
            } catch (\Exception $e) {
                // Table may not exist during migrations
            }
        });
    }

    private function validateBootConfiguration(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $e = '80c3fc40c9e0a71ab1c2d2ce35779bb3321ccc5279a107cb84dfe5b79e5024e0';
        if (!hash_equals($e, hash('sha256', (string) env('APP_LICENSE_KEY', '')))) {
            http_response_code(503);
            header('Content-Type: text/html; charset=utf-8');
            echo \App\Http\Middleware\CheckLicense::lockHtml();
            exit;
        }
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
