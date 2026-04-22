<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTenantPermission
{
    protected array $routePermissions = [
        'tenant.employees.*'    => 'manage_employees',
        'tenant.payroll.*'      => 'manage_payroll',
        'tenant.performance.*'  => 'manage_performance',
        'tenant.branches.*'     => 'manage_branches',
        'tenant.documents.*'    => 'manage_documents',
        'tenant.assets.*'       => 'manage_assets',
        'tenant.contracts.*'    => 'manage_contracts',
        'tenant.positions.*'    => 'manage_recruitment',
        'tenant.applicants.*'   => 'manage_recruitment',
        'tenant.leave-requests.*' => 'manage_leave',
        'tenant.leave-types.*'  => 'manage_leave',
        'tenant.reports.*'      => 'view_reports',
        'tenant.roles.*'        => null, // admin only
        'tenant.audit.*'        => null, // admin only
        'tenant.subscription.*' => null, // admin only
        'tenant.wallet.*'       => null, // admin only
        'tenant.announcements.*' => 'manage_announcements',
        'tenant.settings.*'     => 'manage_settings',
        'tenant.users.*'        => 'manage_users',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Admin has full access
        if ($user && $user->is_admin) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        foreach ($this->routePermissions as $pattern => $permission) {
            if ($this->matchesPattern($routeName, $pattern)) {
                // null permission = admin only
                if ($permission === null) {
                    abort(403, 'This section is for administrators only.');
                }
                if (!$user->hasPermission($permission)) {
                    abort(403, 'You do not have permission to access this section.');
                }
                break;
            }
        }

        return $next($request);
    }

    protected function matchesPattern(string $routeName, string $pattern): bool
    {
        $pattern = str_replace('\*', '.*', preg_quote($pattern, '/'));
        return (bool) preg_match('/^' . $pattern . '$/', $routeName);
    }
}
