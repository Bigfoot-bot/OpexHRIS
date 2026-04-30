<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuperAdminPasswordChanged;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::getSettings();
        return view('central.superadmin.general-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'support_email'       => ['nullable', 'email', 'max:255'],
            'support_phone'       => ['nullable', 'string', 'max:20'],
            'default_timezone'    => ['required', 'timezone'],
            'max_login_attempts'  => ['required', 'integer', 'min:3', 'max:20'],
            'session_lifetime'    => ['required', 'integer', 'min:15', 'max:1440'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],
        ]);

        $settings = SystemSetting::getSettings();

        $settings->update([
            'maintenance_mode'        => $request->boolean('maintenance_mode'),
            'maintenance_message'     => $request->maintenance_message,
            'allow_new_registrations' => $request->boolean('allow_new_registrations'),
            'support_email'           => $request->support_email,
            'support_phone'           => $request->support_phone,
            'default_timezone'        => $request->default_timezone,
            'max_login_attempts'      => $request->max_login_attempts,
            'session_lifetime'        => $request->session_lifetime,
        ]);

        return back()->with('success', 'General settings updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'The new password and confirm password do not match.',
        ]);

        $admin = auth('super_admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()
                ->withErrors(['current_password' => 'The current password you entered is incorrect.'])
                ->withInput()
                ->with('password_error', true);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        try {
            Mail::to($admin->email)->send(new SuperAdminPasswordChanged($admin));
        } catch (\Exception $e) {
            // Don't block the success response if mail fails
        }

        return back()->with('password_success', 'Password changed successfully. A confirmation email has been sent to ' . $admin->email . '.');
    }
}
