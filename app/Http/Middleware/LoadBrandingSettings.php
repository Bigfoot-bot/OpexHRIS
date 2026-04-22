<?php

namespace App\Http\Middleware;

use App\Models\BrandingSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class LoadBrandingSettings
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $branding = BrandingSetting::getSettings();
            View::share('branding', $branding);
        } catch (\Exception $e) {
            // Silently fail
        }

        return $next($request);
    }
}
