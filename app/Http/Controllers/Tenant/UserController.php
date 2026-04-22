<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Auth\InvitationController;
use App\Models\AuditLog;
use App\Models\Tenant\Employee;
use App\Models\Tenant\User;
use App\Models\UserInvitation;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users       = User::with('roles', 'employee')->latest()->get();
        $roles       = Role::where('guard_name', 'web')->get();
        $invitations = UserInvitation::where('tenant_id', tenant('id'))
                            ->whereNull('accepted_at')
                            ->where('expires_at', '>', now())
                            ->latest()->get();
        return view('tenant.users.index', compact('users', 'roles', 'invitations'));
    }

    public function create()
    {
        $roles     = Role::where('guard_name', 'web')->get();
        $employees = Employee::where('employment_status', 'active')->get();
        return view('tenant.users.create', compact('roles', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email'],
            'role'        => ['required', 'string'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'send_invite' => ['boolean'],
        ]);

        if ($request->boolean('send_invite')) {
            // Send invitation instead of creating user directly
            $invitation = UserInvitation::createInvitation(
                tenantId:   tenant('id'),
                email:      $validated['email'],
                name:       $validated['name'],
                role:       $validated['role'],
                employeeId: $validated['employee_id'] ?? null,
            );

            // Send invitation email
            $domain = request()->getHost();
            InvitationController::sendInvitationEmail($invitation, $domain);

            AuditLog::log('invited', 'User', "Sent invitation to {$validated['name']} ({$validated['email']}) for role {$validated['role']}");

            return redirect()->route('tenant.users.index')
                             ->with('success', "Invitation sent to {$validated['email']} successfully!");
        }

        // Create user directly with password
        $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'tenant_id'   => tenant('id'),
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => bcrypt($request->password),
            'employee_id' => $validated['employee_id'] ?? null,
            'status'      => 'active',
        ]);

        $user->assignRole($validated['role']);

        AuditLog::log('created', 'User', "Created user {$user->name} with role {$validated['role']}");

        return redirect()->route('tenant.users.index')
                         ->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $roles     = Role::where('guard_name', 'web')->get();
        $employees = Employee::where('employment_status', 'active')->get();
        return view('tenant.users.edit', compact('user', 'roles', 'employees'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email'],
            'role'        => ['required', 'string'],
            'status'      => ['required', 'in:active,inactive'],
            'employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        $oldRole = $user->roles->first()?->name;

        $user->update([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'status'      => $validated['status'],
            'employee_id' => $validated['employee_id'] ?? null,
        ]);

        $user->syncRoles([$validated['role']]);

        AuditLog::log('updated', 'User', "Updated user {$user->name} — role changed from {$oldRole} to {$validated['role']}");

        return redirect()->route('tenant.users.index')
                         ->with('success', 'User updated successfully!');
    }

    public function resendInvitation(UserInvitation $invitation)
    {
        // Refresh token and expiry
        $invitation->update([
            'token'      => UserInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $domain = request()->getHost();
        InvitationController::sendInvitationEmail($invitation, $domain);

        return back()->with('success', "Invitation resent to {$invitation->email}!");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();
        AuditLog::log('deleted', 'User', "Deleted user {$name}");

        return redirect()->route('tenant.users.index')
                         ->with('success', 'User deleted.');
    }
}