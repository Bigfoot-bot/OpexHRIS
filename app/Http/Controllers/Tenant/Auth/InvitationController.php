<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserInvitation;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function accept(string $token)
    {
        $invitation = UserInvitation::where('token', $token)->first();

        if (!$invitation) {
            return view('tenant.auth.invitation-invalid', [
                'message' => 'This invitation link is invalid.'
            ]);
        }

        if ($invitation->isExpired()) {
            return view('tenant.auth.invitation-invalid', [
                'message' => 'This invitation link has expired. Please contact your HR Administrator.'
            ]);
        }

        if ($invitation->isAccepted()) {
            return view('tenant.auth.invitation-invalid', [
                'message' => 'This invitation has already been accepted. Please login.'
            ]);
        }

        return view('tenant.auth.invitation-accept', compact('invitation'));
    }

    public function store(Request $request, string $token)
    {
        $invitation = UserInvitation::where('token', $token)->first();

        if (!$invitation || $invitation->isExpired() || $invitation->isAccepted()) {
            return redirect()->route('tenant.login')
                             ->with('error', 'This invitation is no longer valid.');
        }

        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // Verify email matches invitation
        if (strtolower($request->email) !== strtolower($invitation->email)) {
            return back()->withErrors([
                'email' => 'This email does not match the invitation. Please use the email address the invitation was sent to.'
            ]);
        }

        $user = User::withoutGlobalScopes()
                    ->where('email', $invitation->email)
                    ->where('tenant_id', $invitation->tenant_id)
                    ->first();

        if (!$user) {
            $user = User::create([
                'tenant_id'   => $invitation->tenant_id,
                'name'        => $invitation->name,
                'email'       => $invitation->email,
                'password'    => Hash::make($request->password),
                'employee_id' => $invitation->employee_id,
                'status'      => 'active',
            ]);
        } else {
            $user->update([
                'password'    => Hash::make($request->password),
                'employee_id' => $invitation->employee_id ?? $user->employee_id,
                'status'      => 'active',
            ]);
        }

        $user->syncRoles([$invitation->role]);
        $invitation->update(['accepted_at' => now()]);
        auth()->login($user);

        if ($user->hasRole('Employee') && !$user->hasAnyRole(['HR Admin', 'HR Manager', 'Department Head'])) {
            $user->update(['portal_preference' => 'employee']);
            return redirect()->route('tenant.employee.dashboard')
                             ->with('success', 'Welcome! Your account has been set up successfully.');
        }

        return redirect()->route('tenant.dashboard')
                         ->with('success', 'Welcome! Your account has been set up successfully.');
    }

    public static function sendInvitationEmail(UserInvitation $invitation, string $domain): void
    {
        $link = 'http://' . $domain . '/invitation/' . $invitation->token;

        Mail::send('emails.invitation', [
            'invitation' => $invitation,
            'link'       => $link,
        ], function ($message) use ($invitation) {
            $message->to($invitation->email, $invitation->name)
                    ->subject('You have been invited to join the HRIS Portal');
        });
    }
}