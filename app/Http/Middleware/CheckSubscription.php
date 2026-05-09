<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\FacilitySubscription;
use App\Models\Central\Tenant;

class CheckSubscription
{
    // Routes allowed when subscription is expired/missing (but NOT suspended)
    protected array $allowedRoutes = [
        'tenant.subscription.index',
        'tenant.subscription.plans',
        'tenant.subscription.checkout',
        'tenant.subscription.checkout.get',
        'tenant.subscription.pay',
        'tenant.subscription.stk-push',
        'tenant.wallet.index',
        'tenant.wallet.top-up',
        'tenant.wallet.submit-top-up',
        'tenant.wallet.stk-push',
        'tenant.support.index',
        'tenant.support.store',
        'tenant.support.show',
        'tenant.logout',
        'tenant.portal.switch',
        'tenant.branch.switch',
    ];

    // Only logout is allowed when suspended — nothing else
    protected array $suspendedAllowedRoutes = [
        'tenant.logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        $tenantId = tenant('id');
        if (!$tenantId) return $next($request);

        $routeName = $request->route()?->getName();

        // — SUSPENDED via is_active flag (set from super admin tenants page) —
        $tenant = Tenant::find($tenantId);
        if ($tenant && !$tenant->is_active) {
            foreach ($this->suspendedAllowedRoutes as $allowed) {
                if ($routeName === $allowed || str_starts_with($routeName ?? '', $allowed)) {
                    return $next($request);
                }
            }
            return $this->showSuspended($request);
        }

        // Allow whitelisted routes for expired/no-subscription states
        foreach ($this->allowedRoutes as $allowed) {
            if ($routeName === $allowed || str_starts_with($routeName ?? '', $allowed)) {
                return $next($request);
            }
        }

        $subscription = FacilitySubscription::where('tenant_id', $tenantId)
                            ->latest()->first();

        // — SUSPENDED via subscription status —
        if ($subscription && $subscription->status === 'suspended') {
            foreach ($this->suspendedAllowedRoutes as $allowed) {
                if ($routeName === $allowed || str_starts_with($routeName ?? '', $allowed)) {
                    return $next($request);
                }
            }
            return $this->showSuspended($request);
        }

        // No subscription at all
        if (!$subscription) {
            return $this->blockAccess($request, 'no_subscription');
        }

        // Trial period — allow access
        if ($subscription->status === 'trial' && $subscription->end_date->isFuture()) {
            return $next($request);
        }

        // Active subscription — allow access
        if ($subscription->status === 'active' && $subscription->end_date->isFuture()) {
            return $next($request);
        }

        // Expired
        return $this->blockAccess($request, $subscription->status);
    }

    protected function showSuspended(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'This facility has been suspended. Please contact support.'], 403);
        }

        return response()->view('tenant.subscription.suspended', [], 403);
    }

    protected function blockAccess(Request $request, string $reason)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->getMessage($reason)], 402);
        }

        return redirect()->route('tenant.subscription.index')
                         ->with('subscription_error', $this->getMessage($reason));
    }

    protected function getMessage(string $reason): string
    {
        return match($reason) {
            'no_subscription' => 'Your facility does not have an active subscription. Please subscribe to continue.',
            'expired'         => 'Your subscription has expired. Please renew to continue using the platform.',
            'suspended'       => 'Your facility has been suspended. Please contact support.',
            default           => 'Your subscription is not active. Please subscribe to continue.',
        };
    }
}
