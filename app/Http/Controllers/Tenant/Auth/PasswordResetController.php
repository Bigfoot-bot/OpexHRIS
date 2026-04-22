<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('tenant.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $domain = $request->getHost();
        $tenant = Tenant::whereHas('domains', function ($q) use ($domain) {
            $q->where('domain', $domain);
        })->first();

        if (!$tenant) {
            return back()->with('error', 'Facility not found.');
        }

        $user = User::withoutGlobalScopes()
                    ->where('email', $request->email)
                    ->where('tenant_id', $tenant->id)
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email address. Please check and try again.'
            ])->withInput();
        }

        // Generate token
        $token = Str::random(64);

        // Store token
        DB::table('tenant_password_reset_tokens')->updateOrInsert(
            ['email' => $request->email, 'tenant_id' => $tenant->id],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // Send email
        $link = 'http://' . $domain . '/reset-password/' . $token . '?email=' . urlencode($request->email);

        Mail::send('emails.password-reset', [
            'user' => $user,
            'link' => $link,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Reset Your HRIS Portal Password');
        });

        return back()->with('success', 'Password reset link has been sent to your email. Please also check your spam folder.');
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('tenant.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $domain = $request->getHost();
        $tenant = Tenant::whereHas('domains', function ($q) use ($domain) {
            $q->where('domain', $domain);
        })->first();

        if (!$tenant) {
            return back()->with('error', 'Facility not found.');
        }

        $record = DB::table('tenant_password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('tenant_id', $tenant->id)
                    ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->with('error', 'This password reset link is invalid or has expired.');
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('tenant_password_reset_tokens')
                ->where('email', $request->email)
                ->where('tenant_id', $tenant->id)
                ->delete();
            return back()->with('error', 'This password reset link has expired. Please request a new one.');
        }

        $user = User::withoutGlobalScopes()
                    ->where('email', $request->email)
                    ->where('tenant_id', $tenant->id)
                    ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        DB::table('tenant_password_reset_tokens')
            ->where('email', $request->email)
            ->where('tenant_id', $tenant->id)
            ->delete();

        return redirect()->route('tenant.login')
                         ->with('success', 'Password reset successfully! Please login with your new password.');
    }
}