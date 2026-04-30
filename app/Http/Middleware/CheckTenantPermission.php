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
        // Employees can view/download documents; only HR/admin can create/edit/delete
        'tenant.documents.create'          => 'manage_documents',
        'tenant.documents.store'           => 'manage_documents',
        'tenant.documents.edit'            => 'manage_documents',
        'tenant.documents.update'          => 'manage_documents',
        'tenant.documents.destroy'         => 'manage_documents',
        'tenant.documents.categories'      => 'manage_documents',
        'tenant.documents.categories.*'    => 'manage_documents',
        // Employees can view their assigned assets; only HR/admin can manage
        'tenant.assets.create'   => 'manage_assets',
        'tenant.assets.store'    => 'manage_assets',
        'tenant.assets.edit'     => 'manage_assets',
        'tenant.assets.update'   => 'manage_assets',
        'tenant.assets.destroy'  => 'manage_assets',
        // Employees can view their contracts; only HR/admin can manage
        'tenant.contracts.create'   => 'manage_contracts',
        'tenant.contracts.store'    => 'manage_contracts',
        'tenant.contracts.edit'     => 'manage_contracts',
        'tenant.contracts.update'   => 'manage_contracts',
        'tenant.contracts.destroy'  => 'manage_contracts',
        'tenant.positions.*'    => 'manage_recruitment',
        'tenant.applicants.*'   => 'manage_recruitment',
        'tenant.leave-requests.*' => 'manage_leave',
        'tenant.leave-types.*'  => 'manage_leave',
        'tenant.reports.*'      => 'view_reports',
        'tenant.roles.*'        => null, // admin only
        'tenant.audit.*'        => null, // admin only
        'tenant.subscription.*' => null, // admin only
        'tenant.wallet.*'       => null, // admin only
        // Employees can view announcements; only admins/HR can create/edit/delete
        'tenant.announcements.create'  => 'manage_announcements',
        'tenant.announcements.store'   => 'manage_announcements',
        'tenant.announcements.edit'    => 'manage_announcements',
        'tenant.announcements.update'  => 'manage_announcements',
        'tenant.announcements.destroy' => 'manage_announcements',
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
