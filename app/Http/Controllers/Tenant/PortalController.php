<?php
namespace App\Http\Controllers\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class PortalController extends Controller
{
    public function switch(Request $request)
    {
        $user = auth()->user();
        $newPreference = $user->portal_preference === 'hr' ? 'employee' : 'hr';

        $user->update(['portal_preference' => $newPreference]);

        if ($newPreference === 'employee') {
            return redirect()->route('tenant.employee.dashboard');
        }

        return redirect()->route('tenant.dashboard');
    }
}
