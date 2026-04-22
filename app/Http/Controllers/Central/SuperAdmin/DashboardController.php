<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\Tenant\Employee;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_facilities'  => Tenant::count(),
            'active_facilities' => Tenant::where('is_active', true)->count(),
            'trial_facilities'  => Tenant::whereNotNull('trial_ends_at')
                                         ->where('trial_ends_at', '>', now())
                                         ->count(),
            'expiring_soon'     => Tenant::where('subscription_ends_at', '<=', now()->addDays(30))
                                         ->where('subscription_ends_at', '>', now())
                                         ->count(),
            'total_employees'   => Employee::withoutGlobalScopes()->count(),
            'total_users'       => User::withoutGlobalScopes()->count(),
            'basic_plan'        => Tenant::where('subscription_plan', 'basic')->count(),
            'professional_plan' => Tenant::where('subscription_plan', 'professional')->count(),
            'enterprise_plan'   => Tenant::where('subscription_plan', 'enterprise')->count(),
        ];

        $recentTenants = Tenant::latest()->take(5)->get();

        $tenantUsage = Tenant::withCount([
            'domains',
        ])->latest()->get()->map(function($tenant) {
            $employeeCount = Employee::withoutGlobalScopes()
                                ->where('tenant_id', $tenant->id)
                                ->count();
            $userCount = User::withoutGlobalScopes()
                                ->where('tenant_id', $tenant->id)
                                ->count();
            $tenant->employee_count = $employeeCount;
            $tenant->user_count = $userCount;
            return $tenant;
        });

        $planRevenue = [
            'basic'        => Tenant::where('subscription_plan', 'basic')->count() * 10000,
            'professional' => Tenant::where('subscription_plan', 'professional')->count() * 25000,
            'enterprise'   => Tenant::where('subscription_plan', 'enterprise')->count() * 90000,
        ];
        $planRevenue['total'] = array_sum($planRevenue);

        return view('central.dashboard.index', compact(
            'stats',
            'recentTenants',
            'tenantUsage',
            'planRevenue'
        ));
    }
}