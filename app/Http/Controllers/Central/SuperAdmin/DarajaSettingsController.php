<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Central\DarajaSetting;
use Illuminate\Http\Request;

class DarajaSettingsController extends Controller
{
    public function index()
    {
        $settings = DarajaSetting::getSettings();
        return view('central.wallet.daraja-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'consumer_key'    => ['required', 'string'],
            'consumer_secret' => ['required', 'string'],
            'paybill_number'  => ['required', 'string'],
            'passkey'         => ['required', 'string'],
            'callback_url'    => ['required', 'url'],
            'environment'     => ['required', 'in:sandbox,production'],
        ]);

        $settings = DarajaSetting::getSettings();
        $settings->update([
            'consumer_key'    => $request->consumer_key,
            'consumer_secret' => $request->consumer_secret,
            'paybill_number'  => $request->paybill_number,
            'passkey'         => $request->passkey,
            'callback_url'    => $request->callback_url,
            'environment'     => $request->environment,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Daraja settings updated successfully!');
    }

    public function testConnection()
    {
        $settings = DarajaSetting::getSettings();

        if (!$settings->is_active) {
            return back()->with('error', 'Daraja is not active. Please enable it first.');
        }

        try {
            $credentials = base64_encode($settings->consumer_key . ':' . $settings->consumer_secret);
            $response    = \Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Basic ' . $credentials,
            ])->get($settings->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials');

            if ($response->successful() && isset($response->json()['access_token'])) {
                return back()->with('success', 'Daraja connection successful! Access token received.');
            }

            return back()->with('error', 'Connection failed: ' . $response->status() . ' - ' . $response->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Connection failed: ' . $e->getMessage());
        }
    }
}


