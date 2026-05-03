<?php
namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BrandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'platform_name'       => ['required', 'string', 'max:100'],
            'platform_tagline'    => ['nullable', 'string', 'max:200'],
            'primary_color'       => ['nullable', 'string', 'max:20'],
            'logo'                => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
            'favicon'             => ['nullable', 'file', 'mimes:png,jpg,jpeg,ico', 'max:512'],
            'bank_name'           => ['nullable', 'string', 'max:200'],
            'bank_account_name'   => ['nullable', 'string', 'max:200'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_branch'         => ['nullable', 'string', 'max:200'],
            'paybill_number'      => ['nullable', 'string', 'max:20'],
            'mpesa_account'       => ['nullable', 'string', 'max:100'],
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

        $brandingPath = public_path('branding');
        if (!is_dir($brandingPath)) {
            mkdir($brandingPath, 0777, true);
        }

        foreach (['logo', 'favicon'] as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                if ($settings->$field && file_exists(public_path('branding/' . $settings->$field))) {
                    unlink(public_path('branding/' . $settings->$field));
                }
                $filename = $field . '_' . time() . '.' . $request->file($field)->extension();
                $request->file($field)->move($brandingPath, $filename);
                $data[$field] = $filename;
            }
        }

        try {
            $settings->update($data);
        } catch (\Exception $e) {
            Log::error('Branding settings save failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to save settings: ' . $e->getMessage())->withInput();
        }

        return back()->with('success', 'Branding settings updated successfully!');
    }
}
