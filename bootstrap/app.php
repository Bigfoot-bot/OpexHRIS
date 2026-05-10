<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/tenant.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'daraja/callback',
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->getHost() !== 'hris-platform.test') {
                return 'http://' . $request->getHost() . '/login';
            }
            return 'http://hris-platform.test/login';
        });
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\LoadBrandingSettings::class,
        ]);
        $middleware->alias([
            'tenant.permission'    => \App\Http\Middleware\CheckTenantPermission::class,
            'tenant.subscription'  => \App\Http\Middleware\CheckSubscription::class,
            'tenant.ip'            => \App\Http\Middleware\CheckIpWhitelist::class,
            'tenant.mfa'           => \App\Http\Middleware\CheckMfa::class,
            'maintenance'          => \App\Http\Middleware\MaintenanceMode::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();











