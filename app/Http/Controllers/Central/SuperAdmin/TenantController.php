<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\Central\SuperAdmin;
use App\Mail\FacilityRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::latest()->paginate(10);
        return view('central.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('central.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:tenants,email'],
            'phone'             => ['required', 'string', 'max:20'],
            'subscription_plan' => ['required', 'in:basic,professional,enterprise'],
            'admin_name'        => ['required', 'string', 'max:255'],
            'admin_email'       => ['required', 'email'],
            'admin_password'    => ['required', 'string', 'min:8'],
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $tenant = Tenant::create([
            'id'                => Str::uuid(),
            'name'              => $validated['name'],
            'slug'              => $slug,
            'email'             => $validated['email'],
            'phone'             => $validated['phone'],
            'subscription_plan' => $validated['subscription_plan'],
            'is_active'         => true,
            'trial_ends_at'     => now()->addDays(14),
        ]);

        $tenant->domains()->create([
            'domain' => $slug . '.hris-platform.test',
        ]);

        \App\Models\Tenant\User::create([
            'tenant_id' => $tenant->id,
            'name'      => $validated['admin_name'],
            'email'     => $validated['admin_email'],
            'password'  => bcrypt($validated['admin_password']),
            'status'    => 'active',
        ]);

        // Send email notification to Super Admin
        try {
            $superAdmin = SuperAdmin::first();
            if ($superAdmin) {
                Mail::to($superAdmin->email)->send(new FacilityRegistered($tenant));
            }
        } catch (\Exception $e) {
            // Silently fail — don't block facility creation
        }

        return redirect()->route('admin.tenants.index')
                         ->with('success', "Facility '{$tenant->name}' has been onboarded successfully!");
    }

    public function show(Tenant $tenant)
    {
        return view('central.tenants.show', compact('tenant'));
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);
        $status = $tenant->is_active ? 'activated' : 'suspended';
        return back()->with('success', "Facility has been {$status} successfully.");
    }

    public function updatePlan(Request $request, Tenant $tenant)
    {
        $request->validate([
            'subscription_plan' => ['required', 'in:basic,professional,enterprise'],
        ]);

        $tenant->update([
            'subscription_plan'    => $request->subscription_plan,
            'subscription_ends_at' => now()->addMonth(),
        ]);

        return back()->with('success', "Subscription plan updated to {$request->subscription_plan} successfully!");
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('admin.tenants.index')
                         ->with('success', 'Facility has been removed.');
    }
}
