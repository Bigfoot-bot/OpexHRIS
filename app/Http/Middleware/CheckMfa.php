<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckMfa
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user || !$user->mfa_enabled) {
            return $next($request);
        }

        // Skip MFA routes
        if ($request->routeIs('tenant.mfa.*') || $request->routeIs('tenant.logout')) {
            return $next($request);
        }

        // Check if MFA already verified this session
        if (Session::get('mfa_verified')) {
            return $next($request);
        }

        return redirect()->route('tenant.mfa.challenge');
    }
}
