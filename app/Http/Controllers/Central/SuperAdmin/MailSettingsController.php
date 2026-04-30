<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Central\MailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailSettingsController extends Controller
{
    public function index()
    {
        $settings = MailSetting::getSettings();
        return view('central.superadmin.mail-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mail_username' => ['required', 'email'],
            'mail_password' => ['required'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        $fromAddress = $request->filled('mail_from_address') ? $request->mail_from_address : $request->mail_username;

        $settings = MailSetting::getSettings();
        $settings->update([
            'mail_username'     => $request->mail_username,
            'mail_password'     => $request->mail_password,
            'mail_from_address' => $fromAddress,
            'mail_from_name'    => $request->mail_from_name,
            'is_configured'     => true,
        ]);

        // Apply immediately so test email works in same session
        config([
            'mail.default'                 => 'smtp',
            'mail.mailers.smtp.host'       => $settings->mail_host,
            'mail.mailers.smtp.port'       => $settings->mail_port,
            'mail.mailers.smtp.username'   => $settings->mail_username,
            'mail.mailers.smtp.password'   => $settings->mail_password,
            'mail.mailers.smtp.encryption' => $settings->mail_encryption,
            'mail.from.address'            => $fromAddress,
            'mail.from.name'               => $settings->mail_from_name,
        ]);

        return back()->with('success', 'Mail settings saved successfully.');
    }

    public function sendTest(Request $request)
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $settings = MailSetting::getSettings();

        if (!$settings->is_configured) {
            return back()->with('error', 'Please save your mail settings before sending a test email.');
        }

        try {
            config([
                'mail.mailers.smtp.host'       => $settings->mail_host,
                'mail.mailers.smtp.port'       => $settings->mail_port,
                'mail.mailers.smtp.username'   => $settings->mail_username,
                'mail.mailers.smtp.password'   => $settings->mail_password,
                'mail.mailers.smtp.encryption' => $settings->mail_encryption,
                'mail.from.address'            => $settings->mail_from_address,
                'mail.from.name'               => $settings->mail_from_name,
            ]);

            Mail::raw('This is a test email from OpEx HRIS. Your mail settings are working correctly!', function ($message) use ($request, $settings) {
                $message->to($request->test_email)
                        ->subject('OpEx HRIS � Test Email');
            });

            return back()->with('success', 'Test email sent successfully to ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
