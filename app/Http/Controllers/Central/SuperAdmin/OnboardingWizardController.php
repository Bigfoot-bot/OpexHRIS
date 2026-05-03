<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\FacilitySubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingWizardController extends Controller
{
    // Step 1 — Facility Profile
    public function step1()
    {
        $plans     = SubscriptionPlan::where('is_active', true)->orderBy('monthly_price')->get();
        $discounts = \App\Models\SubscriptionDiscount::all()->keyBy('cycle');
        return view('central.onboarding.step1', compact('plans', 'discounts'));
    }

    public function step1Store(Request $request)
    {
        $planIds = SubscriptionPlan::where('is_active', true)->pluck('id')->toArray();

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:tenants,email'],
            'phone'             => ['required', 'string', 'max:20'],
            'facility_type'     => ['required', 'string'],
            'county'            => ['required', 'string'],
            'address'           => ['nullable', 'string'],
            'keph_level'        => ['nullable', 'string'],
            'bed_capacity'      => ['nullable', 'integer'],
            'subscription_plan' => ['required', 'integer', 'in:' . implode(',', $planIds)],
            'billing_cycle'     => ['required', 'in:monthly,quarterly,biannual,annual'],
        ]);

        // Store in session
        session(['wizard.step1' => $validated]);

        return redirect()->route('admin.onboarding.step2');
    }

    // Step 2 — Departments
    public function step2()
    {
        if (!session('wizard.step1')) {
            return redirect()->route('admin.onboarding.step1');
        }
        return view('central.onboarding.step2');
    }

    public function step2Store(Request $request)
    {
        $request->validate([
            'departments'   => ['required', 'array', 'min:1'],
            'departments.*' => ['required', 'string', 'max:100'],
        ]);

        session(['wizard.step2' => $request->departments]);

        return redirect()->route('admin.onboarding.step3');
    }

    // Step 3 — Leave Types
    public function step3()
    {
        if (!session('wizard.step1')) {
            return redirect()->route('admin.onboarding.step1');
        }
        return view('central.onboarding.step3');
    }

    public function step3Store(Request $request)
    {
        $request->validate([
            'leave_types'              => ['required', 'array', 'min:1'],
            'leave_types.*.name'       => ['required', 'string'],
            'leave_types.*.days'       => ['required', 'integer', 'min:1'],
        ]);

        session(['wizard.step3' => $request->leave_types]);

        return redirect()->route('admin.onboarding.step4');
    }

    // Step 4 — First HR Admin
    public function step4()
    {
        if (!session('wizard.step1')) {
            return redirect()->route('admin.onboarding.step1');
        }
        return view('central.onboarding.step4');
    }

    public function step4Store(Request $request)
    {
        $request->validate([
            'admin_name'     => ['required', 'string', 'max:255'],
            'admin_email'    => ['required', 'email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        session(['wizard.step4' => $request->only('admin_name', 'admin_email', 'admin_password')]);

        return redirect()->route('admin.onboarding.step5');
    }

    // Step 5 — Review & Complete
    public function step5()
    {
        if (!session('wizard.step1')) {
            return redirect()->route('admin.onboarding.step1');
        }

        $data = [
            'step1' => session('wizard.step1'),
            'step2' => session('wizard.step2', []),
            'step3' => session('wizard.step3', []),
            'step4' => session('wizard.step4'),
        ];

        return view('central.onboarding.step5', compact('data'));
    }

    public function complete(Request $request)
    {
        $step1 = session('wizard.step1');
        $step2 = session('wizard.step2', []);
        $step3 = session('wizard.step3', []);
        $step4 = session('wizard.step4');

        if (!$step1 || !$step4) {
            return redirect()->route('admin.onboarding.step1')
                             ->with('error', 'Please complete all steps.');
        }

        // Create tenant
        $slug = Str::slug($step1['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $selectedPlan = SubscriptionPlan::find($step1['subscription_plan']);

        $tenant = Tenant::create([
            'id'                => Str::uuid(),
            'name'              => $step1['name'],
            'slug'              => $slug,
            'email'             => $step1['email'],
            'phone'             => $step1['phone'],
            'facility_type'     => $step1['facility_type'],
            'county'            => $step1['county'],
            'address'           => $step1['address'] ?? null,
            'keph_level'        => $step1['keph_level'] ?? null,
            'bed_capacity'      => $step1['bed_capacity'] ?? null,
            'subscription_plan' => $selectedPlan ? strtolower($selectedPlan->name) : 'basic',
            'is_active'         => true,
            'trial_ends_at'     => now()->addDays(14),
        ]);

        $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
        $tenant->domains()->create([
            'domain' => $slug . '.' . $baseHost,
        ]);

        // Create admin employee record so the admin can access the employee portal
        $nameParts = explode(' ', trim($step4['admin_name']), 2);
        $employee  = \App\Models\Tenant\Employee::create([
            'tenant_id'          => $tenant->id,
            'employee_number'    => 'EMP0001',
            'first_name'         => $nameParts[0],
            'last_name'          => $nameParts[1] ?? $nameParts[0],
            'email'              => $step4['admin_email'],
            'employment_type'    => 'permanent',
            'employment_status'  => 'active',
            'hire_date'          => now()->toDateString(),
            'nationality'        => 'Kenyan',
        ]);

        // Create HR Admin user linked to the employee record
        $user = \App\Models\Tenant\User::create([
            'tenant_id'   => $tenant->id,
            'name'        => $step4['admin_name'],
            'email'       => $step4['admin_email'],
            'password'    => bcrypt($step4['admin_password']),
            'status'      => 'active',
            'is_admin'    => true,
            'is_hr'       => true,
            'employee_id' => $employee->id,
        ]);

        // Assign HR Admin role
        $user->assignRole('HR Admin');

        // Create trial subscription using the selected billing cycle
        if ($selectedPlan) {
            $cycle  = $step1['billing_cycle'] ?? 'monthly';
            $months = \App\Models\SubscriptionDiscount::getMonths($cycle);
            FacilitySubscription::create([
                'tenant_id'      => $tenant->id,
                'plan_id'        => $selectedPlan->id,
                'cycle'          => $cycle,
                'amount_paid'    => 0.00,
                'vat_amount'     => 0.00,
                'discount_amount'=> 0.00,
                'start_date'     => now()->toDateString(),
                'end_date'       => now()->addMonths($months)->toDateString(),
                'status'         => 'trial',
                'auto_renew'     => false,
            ]);
        }

        // Create departments in tenant settings
        if (!empty($step2)) {
            \App\Models\TenantSetting::create([
                'tenant_id' => $tenant->id,
                'key'       => 'departments',
                'value'     => json_encode($step2),
            ]);
        }

        // Create leave types
        if (!empty($step3)) {
            foreach ($step3 as $leaveType) {
                \App\Models\Tenant\LeaveType::create([
                    'tenant_id'   => $tenant->id,
                    'name'        => $leaveType['name'],
                    'days_allowed' => $leaveType['days'],
                    'is_active'   => true,
                ]);
            }
        }

        // Clear wizard session
        session()->forget(['wizard.step1', 'wizard.step2', 'wizard.step3', 'wizard.step4']);

        return redirect()->route('admin.tenants.show', $tenant)
                         ->with('success', "Facility '{$tenant->name}' has been onboarded successfully!");
    }
}