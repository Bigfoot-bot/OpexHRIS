<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantRole;
use App\Models\TenantPermission;
use App\Models\TenantUserRole;
use App\Models\Tenant\Employee;
use App\Models\Tenant\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = TenantRole::where('tenant_id', tenant('id'))
                           ->withCount('users')
                           ->with('permissions')
                           ->get();
        $allPermissions = TenantRole::allPermissions();
        $employees      = Employee::orderBy('first_name')->get();
        $admins         = User::where('tenant_id', tenant('id'))
                              ->where('is_admin', true)
                              ->with('employee')
                              ->get();
        return view('tenant.roles.index', compact('roles', 'allPermissions', 'employees', 'admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'permissions' => ['nullable', 'array'],
        ]);

        $role = TenantRole::create([
            'tenant_id'   => tenant('id'),
            'name'        => $request->name,
            'description' => $request->description,
            'is_admin'    => false,
        ]);

        if ($request->permissions) {
            foreach ($request->permissions as $permission) {
                TenantPermission::create([
                    'tenant_id'  => tenant('id'),
                    'role_id'    => $role->id,
                    'permission' => $permission,
                ]);
            }
        }

        return back()->with('success', 'Role created successfully!');
    }

    public function update(Request $request, TenantRole $role)
    {
        if ($role->tenant_id !== tenant('id')) abort(403);
        $request->validate(['name' => ['required', 'string', 'max:100']]);

        $role->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        // Sync permissions
        TenantPermission::where('role_id', $role->id)->delete();
        if ($request->permissions) {
            foreach ($request->permissions as $permission) {
                TenantPermission::create([
                    'tenant_id'  => tenant('id'),
                    'role_id'    => $role->id,
                    'permission' => $permission,
                ]);
            }
        }

        return back()->with('success', 'Role updated successfully!');
    }

    public function destroy(TenantRole $role)
    {
        if ($role->tenant_id !== tenant('id')) abort(403);
        TenantPermission::where('role_id', $role->id)->delete();
        TenantUserRole::where('role_id', $role->id)->delete();
        $role->delete();
        return back()->with('success', 'Role deleted successfully!');
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:tenant_users,id'],
            'role_id' => ['required', 'exists:tenant_roles,id'],
        ]);

        TenantUserRole::firstOrCreate([
            'tenant_id' => tenant('id'),
            'user_id'   => $request->user_id,
            'role_id'   => $request->role_id,
        ]);

        return back()->with('success', 'Role assigned successfully!');
    }

    public function revokeRole(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
            'role_id' => ['required'],
        ]);

        TenantUserRole::where('user_id', $request->user_id)
                      ->where('role_id', $request->role_id)
                      ->delete();

        return back()->with('success', 'Role revoked successfully!');
    }

    public function assignAdmin(Request $request)
    {
        $request->validate(['employee_id' => ['required', 'exists:employees,id']]);

        $employee = Employee::findOrFail($request->employee_id);
        $user = User::where('tenant_id', tenant('id'))
                    ->where('employee_id', $employee->id)
                    ->first();

        if (!$user) {
            return back()->with('error', "{$employee->full_name} does not have a user account yet. Create one first under Users.");
        }

        $user->update(['is_admin' => true, 'is_hr' => true]);

        return back()->with('success', "{$user->name} has been granted Facility Admin access.");
    }

    public function revokeAdmin(Request $request)
    {
        $request->validate(['user_id' => ['required', 'exists:tenant_users,id']]);

        $user = User::findOrFail($request->user_id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove your own admin access.');
        }

        $user->update(['is_admin' => false, 'is_hr' => false]);

        return back()->with('success', "{$user->name}'s admin access has been revoked.");
    }

    public function users()
    {
        $users = User::where('tenant_id', tenant('id'))
                     ->with('tenantRoles.role')
                     ->get();
        $roles = TenantRole::where('tenant_id', tenant('id'))->get();
        return view('tenant.roles.users', compact('users', 'roles'));
    }
}
