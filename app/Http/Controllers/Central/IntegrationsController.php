<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\PlatformSetting;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;

class IntegrationsController extends Controller
{
    public function index()
    {
        $settings = PlatformSetting::all()->groupBy('group');
        $tenants  = Tenant::where('is_active', true)->orderBy('name')->get();
        return view('central.integrations.index', compact('settings', 'tenants'));
    }

    public function update(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            $setting = PlatformSetting::where('key', $key)->first();
            if (!$setting) continue;
            if ($setting->is_encrypted && !empty($value)) {
                $value = encrypt($value);
            }
            $setting->update(['value' => $value]);
        }
        return back()->with('success', 'Integration settings saved successfully!');
    }

    public function testSms(Request $request)
    {
        $request->validate(['phone' => ['required', 'string']]);
        try {
            $service = app(\App\Services\SmsService::class);
            $service->send($request->phone, 'Test SMS from OpEx HRIS Platform. If you received this, SMS is configured correctly!');
            return back()->with('success', 'Test SMS sent to ' . $request->phone);
        } catch (\Exception $e) {
            return back()->with('error', 'SMS failed: ' . $e->getMessage());
        }
    }

    public function bulkSms(Request $request)
    {
        $request->validate([
            'message'    => ['required', 'string', 'max:160'],
            'tenant_ids' => ['required', 'array', 'min:1'],
        ]);

        $tenants = Tenant::whereIn('id', $request->tenant_ids)
                         ->where('is_active', true)
                         ->whereNotNull('phone')
                         ->where('phone', '!=', '')
                         ->get();

        if ($tenants->isEmpty()) {
            return back()->with('error', 'None of the selected tenants have a phone number on file.');
        }

        $service = app(\App\Services\SmsService::class);
        $sent    = 0;
        $failed  = 0;

        foreach ($tenants as $tenant) {
            try {
                $service->send($tenant->phone, $request->message);
                $sent++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        $msg = "Bulk SMS sent: {$sent} delivered.";
        if ($failed > 0) $msg .= " {$failed} failed (no phone or error).";

        return back()->with('success', $msg);
    }
}
