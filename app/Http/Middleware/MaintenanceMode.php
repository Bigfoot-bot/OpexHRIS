<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Super admins can always access everything
        if (auth('super_admin')->check()) {
            return $next($request);
        }

        $settings = SystemSetting::getSettings();

        if ($settings->maintenance_mode) {
            return response()->view('errors.maintenance', [
                'message' => $settings->maintenance_message,
            ], 503);
        }

        return $next($request);
    }
}
