<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('central.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $admin = SuperAdmin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors([
                'email' => 'No account found with this email address.'
            ])->withInput();
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $link = url('/admin/reset-password/' . $token . '?email=' . urlencode($request->email));

        Mail::send('emails.password-reset', [
            'user' => $admin,
            'link' => $link,
        ], function ($message) use ($admin) {
            $message->to($admin->email, $admin->name)
                    ->subject('Reset Your Super Admin Password');
        });

        return back()->with('success', 'Password reset link has been sent to your email.');
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('central.auth.reset-password', [
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

        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->with('error', 'This password reset link is invalid or has expired.');
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();
            return back()->with('error', 'This password reset link has expired. Please request a new one.');
        }

        $admin = SuperAdmin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('admin.login')
                         ->with('success', 'Password reset successfully! Please login with your new password.');
    }
}