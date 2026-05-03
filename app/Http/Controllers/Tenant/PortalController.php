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

        if ($newPreference === 'employee' && !$user->employee_id) {
            return back()->with('error', 'Your account is not linked to an employee profile. Please link your user account to an employee record before switching portals.');
        }

        $user->update(['portal_preference' => $newPreference]);

        if ($newPreference === 'employee') {
            return redirect()->route('tenant.employee.dashboard');
        }

        return redirect()->route('tenant.dashboard');
    }
}
