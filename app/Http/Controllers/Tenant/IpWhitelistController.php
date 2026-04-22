<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\IpWhitelist;
use Illuminate\Http\Request;

class IpWhitelistController extends Controller
{
    public function index()
    {
        $ips = IpWhitelist::where('tenant_id', tenant('id'))->latest()->get();
        return view('tenant.ip-whitelist.index', compact('ips'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => ['required', 'string'],
            'label'      => ['nullable', 'string'],
        ]);

        IpWhitelist::create([
            'tenant_id'  => tenant('id'),
            'ip_address' => $request->ip_address,
            'label'      => $request->label,
            'is_active'  => true,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'IP address added to whitelist!');
    }

    public function toggle(IpWhitelist $ip)
    {
        if ($ip->tenant_id !== tenant('id')) abort(403);
        $ip->update(['is_active' => !$ip->is_active]);
        return back()->with('success', 'IP status updated!');
    }

    public function destroy(IpWhitelist $ip)
    {
        if ($ip->tenant_id !== tenant('id')) abort(403);
        $ip->delete();
        return back()->with('success', 'IP address removed!');
    }
}
