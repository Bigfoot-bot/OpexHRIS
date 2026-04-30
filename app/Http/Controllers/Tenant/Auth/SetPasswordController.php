<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordSetConfirmation;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SetPasswordController extends Controller
{
    public function show(Request $request)
    {
        return view('tenant.auth.set-password', [
            'email' => $request->query('email', ''),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'            => ['required', 'email'],
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'The new password and confirm password do not match.',
            'password.min'       => 'Your new password must be at least 8 characters.',
        ]);

        // Find user scoped to this tenant
        $user = User::withoutGlobalScopes()
                    ->where('email', $request->email)
                    ->where('tenant_id', tenant('id'))
                    ->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'No account found with this email address.']);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['current_password' => 'The temporary password you entered is incorrect.']);
        }

        // Update password
        $user->update(['password' => Hash::make($request->password)]);

        // Send confirmation email
        try {
            $loginLink = 'http://' . $request->getHost() . '/login';
            Mail::to($user->email)->send(new PasswordSetConfirmation($user, tenant('name') ?? 'HRIS Portal', $loginLink));
        } catch (\Exception $e) {
            // Silently fail — password is already updated
        }

        return redirect()->route('tenant.login')
                         ->with('success', 'Password set successfully! You can now log in with your email and new password.');
    }
}
