<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\PlatformSetting;
use Illuminate\Http\Request;

class IntegrationsController extends Controller
{
    public function index()
    {
        $settings = PlatformSetting::all()->groupBy('group');
        return view('central.integrations.index', compact('settings'));
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
}
