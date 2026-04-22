<?php
namespace App\Http\Controllers\Central\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\BrandingSetting;
use Illuminate\Http\Request;
class BrandingSettingsController extends Controller
{
    public function index()
    {
        $settings = BrandingSetting::getSettings();
        return view('central.superadmin.branding-settings', compact('settings'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'platform_name'    => ['required', 'string', 'max:100'],
            'platform_tagline' => ['nullable', 'string', 'max:200'],
            'primary_color'    => ['nullable', 'string'],
            'logo'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
            'favicon'          => ['nullable', 'image', 'mimes:png,jpg,ico', 'max:512'],
        ]);
        $settings = BrandingSetting::getSettings();
        $data = [
            'platform_name'       => $request->platform_name,
            'platform_tagline'    => $request->platform_tagline,
            'primary_color'       => $request->primary_color ?? '#064e3b',
            'bank_name'           => $request->bank_name,
            'bank_account_name'   => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_branch'         => $request->bank_branch,
            'paybill_number'      => $request->paybill_number,
            'mpesa_account'       => $request->mpesa_account,
        ];
        if ($request->hasFile('logo')) {
            $filename = 'logo_' . time() . '.' . $request->logo->extension();
            $request->logo->move(public_path('branding'), $filename);
            $data['logo'] = $filename;
        }
        if ($request->hasFile('favicon')) {
            $filename = 'favicon_' . time() . '.' . $request->favicon->extension();
            $request->favicon->move(public_path('branding'), $filename);
            $data['favicon'] = $filename;
        }
        $settings->update($data);
        return back()->with('success', 'Branding settings updated successfully!');
    }
}

