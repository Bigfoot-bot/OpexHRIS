<?php
namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;

class MfaSettingsController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('domains')->get();
        return view('central.mfa.index', compact('tenants'));
    }

    public function update(Request $request)
    {
        $tenantId  = $request->tenant_id;
        $mfaForced = $request->boolean('mfa_forced');

        $tenant = Tenant::findOrFail($tenantId);
        $tenant->update(['mfa_forced' => $mfaForced]);

        return back()->with('success', 'MFA settings updated for ' . $tenant->name);
    }
}
