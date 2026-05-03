<?php
namespace App\Http\Controllers\Tenant\Auth;
use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class LoginController extends Controller
{
    public function show()
    {
        return view('tenant.auth.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $domain = $request->getHost();
        $tenant = Tenant::whereHas('domains', function ($q) use ($domain) {
            $q->where('domain', $domain);
        })->first();

        if (!$tenant) {
            return back()->with('error', 'Facility not found.');
        }

        $user = \App\Models\Tenant\User::withoutGlobalScopes()
                    ->where('email', $credentials['email'])
                    ->where('tenant_id', $tenant->id)
                    ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'These credentials do not match our records.');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);
        return $this->redirectBasedOnRole($user);
    }
    protected function redirectBasedOnRole($user): \Illuminate\Http\RedirectResponse
    {
        if ($user->is_admin) {
            if ($user->employee_id && $user->portal_preference === 'employee') {
                return redirect()->route('tenant.employee.dashboard');
            }
            $user->update(['portal_preference' => 'hr']);
            return redirect()->route('tenant.dashboard');
        }
        if ($user->hasAnyRole(['Branch Manager', 'Branch HR']) && $user->branch_id) {
            if ($user->portal_preference === 'employee' && $user->employee_id) {
                return redirect()->route('tenant.employee.dashboard');
            }
            $branch = \App\Models\Branch::find($user->branch_id);
            if ($branch) return redirect()->route('tenant.branch.dashboard', $branch);
        }
        if ($user->tenantRoles()->count() > 0) {
            $user->update(['portal_preference' => 'hr']);
            return redirect()->route('tenant.dashboard');
        }
        $user->update(['portal_preference' => 'employee']);
        return redirect()->route('tenant.employee.dashboard');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tenant.login');
    }
}
