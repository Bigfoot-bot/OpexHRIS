<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\SubscriptionPlan;
use App\Models\Tenant\Employee;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalFacilities = Tenant::count();

        $stats = [
            'total_facilities'  => $totalFacilities,
            'active_facilities' => Tenant::where('is_active', true)->count(),
            'trial_facilities'  => Tenant::whereNotNull('trial_ends_at')
                                         ->where('trial_ends_at', '>', now())
                                         ->count(),
            'expiring_soon'     => Tenant::where('subscription_ends_at', '<=', now()->addDays(30))
                                         ->where('subscription_ends_at', '>', now())
                                         ->count(),
            'total_employees'   => Employee::withoutGlobalScopes()->count(),
            'total_users'       => User::withoutGlobalScopes()->count(),
        ];

        // Count tenants per plan from actual DB values
        $tenantCountsByPlan = Tenant::select('subscription_plan', DB::raw('count(*) as count'))
            ->groupBy('subscription_plan')
            ->pluck('count', 'subscription_plan');

        // Build plan distribution from SubscriptionPlan table + any orphan plans on tenants
        $allPlanNames = SubscriptionPlan::where('is_active', true)
            ->orderBy('monthly_price')
            ->pluck('monthly_price', 'name');

        // Merge in any tenant plans not in subscription_plans table
        $tenantCountsByPlan->keys()->each(function ($planName) use (&$allPlanNames) {
            if (!$allPlanNames->has($planName)) {
                $allPlanNames->put($planName, 0);
            }
        });

        $planDistribution = $allPlanNames->map(function ($price, $name) use ($tenantCountsByPlan, $totalFacilities) {
            $count = $tenantCountsByPlan->get($name, 0);
            return [
                'name'       => $name,
                'count'      => $count,
                'price'      => $price,
                'revenue'    => $count * $price,
                'percentage' => $totalFacilities > 0 ? round(($count / $totalFacilities) * 100) : 0,
            ];
        });

        $totalRevenue = $planDistribution->sum('revenue');

        $recentTenants = Tenant::latest()->take(5)->get();

        $tenantUsage = Tenant::withCount(['domains'])->latest()->get()->map(function ($tenant) {
            $tenant->employee_count = Employee::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count();
            $tenant->user_count     = User::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count();
            return $tenant;
        });

        return view('central.dashboard.index', compact(
            'stats',
            'planDistribution',
            'totalRevenue',
            'recentTenants',
            'tenantUsage',
        ));
    }
}