<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\FacilitySubscription;

class CheckSubscription
{
    protected array $allowedRoutes = [
        'tenant.subscription.index',
        'tenant.subscription.plans',
        'tenant.subscription.pay',
        'tenant.wallet.index',
        'tenant.wallet.topup',
        'tenant.wallet.store',
        'tenant.support.index',
        'tenant.support.store',
        'tenant.support.show',
        'tenant.logout',
        'tenant.portal.switch',
        'tenant.branch.switch',
        'tenant.employee.dashboard',
        'tenant.employee.profile',
        'tenant.notifications.index',
    ];

    public function handle(Request $request, Closure $next)
    {
        $tenantId = tenant('id');
        if (!$tenantId) return $next($request);

        $subscription = FacilitySubscription::where('tenant_id', $tenantId)
                            ->latest()->first();

        $routeName = $request->route()?->getName();

        // Allow whitelisted routes
        foreach ($this->allowedRoutes as $allowed) {
            if ($routeName === $allowed || str_starts_with($routeName ?? '', $allowed)) {
                return $next($request);
            }
        }

        // No subscription at all
        if (!$subscription) {
            return $this->blockAccess($request, 'no_subscription');
        }

        // Trial period - allow access
        if ($subscription->status === 'trial' && $subscription->end_date->isFuture()) {
            return $next($request);
        }

        // Active subscription - allow access
        if ($subscription->status === 'active' && $subscription->end_date->isFuture()) {
            return $next($request);
        }

        // Expired or suspended
        return $this->blockAccess($request, $subscription->status);
    }

    protected function blockAccess(Request $request, string $reason)
    {
        $user = auth()->user();

        // Employees see a simple message
        if ($user && !$user->is_admin && $user->tenantRoles()->count() === 0) {
            return response()->view('tenant.subscription.suspended-employee', compact('reason'), 402);
        }

        // Admins/HR see subscription page
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
