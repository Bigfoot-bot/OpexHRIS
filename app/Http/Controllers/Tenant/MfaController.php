<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class MfaController extends Controller
{
    public function challenge()
    {
        if (Session::get('mfa_verified')) {
            return redirect()->route('tenant.dashboard');
        }
        return view('tenant.mfa.challenge');
    }

    public function sendCode(Request $request)
    {
        $user = auth()->user();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'mfa_code'            => bcrypt($code),
            'mfa_code_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send via email
        Mail::send('emails.mfa-code', ['code' => $code, 'user' => $user], function ($m) use ($user) {
            $m->to($user->email, $user->name)->subject('Your MFA Verification Code - ' . tenant('name'));
        });

        return back()->with('success', 'Verification code sent to ' . $user->email);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $user = auth()->user();

        if (!$user->mfa_code || !$user->mfa_code_expires_at) {
            return back()->with('error', 'No code found. Please request a new code.');
        }

        if (Carbon::now()->isAfter($user->mfa_code_expires_at)) {
            return back()->with('error', 'Code has expired. Please request a new code.');
        }

        if (!password_verify($request->code, $user->mfa_code)) {
            return back()->with('error', 'Invalid verification code. Please try again.');
        }

        // Clear code and mark verified
        $user->update(['mfa_code' => null, 'mfa_code_expires_at' => null]);
        Session::put('mfa_verified', true);

        return redirect()->route('tenant.dashboard');
    }

    public function setup()
    {
        return view('tenant.mfa.setup');
    }

    public function enable(Request $request)
    {
        $user = auth()->user();
        $user->update(['mfa_enabled' => true, 'mfa_method' => 'email']);
        Session::put('mfa_verified', true);
        return back()->with('success', 'MFA enabled successfully! You will be required to verify your identity on next login.');
    }

    public function disable(Request $request)
    {
        $user = auth()->user();
        $user->update(['mfa_enabled' => false, 'mfa_secret' => null, 'mfa_code' => null]);
        Session::forget('mfa_verified');
        return back()->with('success', 'MFA disabled successfully!');
    }
}
