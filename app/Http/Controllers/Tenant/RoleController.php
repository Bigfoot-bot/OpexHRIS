<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantRole;
use App\Models\TenantPermission;
use App\Models\TenantUserRole;
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
        return view('tenant.roles.index', compact('roles', 'allPermissions'));
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

    public function users()
    {
        $users = User::where('tenant_id', tenant('id'))
                     ->with('tenantRoles.role')
                     ->get();
        $roles = TenantRole::where('tenant_id', tenant('id'))->get();
        return view('tenant.roles.users', compact('users', 'roles'));
    }
}
