<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionDiscount;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans     = SubscriptionPlan::latest()->get();
        $discounts = SubscriptionDiscount::all()->keyBy('cycle');
        return view('central.subscriptions.plans', compact('plans', 'discounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'max_employees' => ['required', 'integer', 'min:1'],
            'is_featured'   => ['nullable', 'boolean'],
        ]);

        SubscriptionPlan::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'monthly_price' => $request->monthly_price,
            'max_employees' => $request->max_employees,
            'is_featured'   => $request->boolean('is_featured'),
            'is_active'     => true,
        ]);

        return back()->with('success', 'Subscription plan created successfully!');
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'max_employees' => ['required', 'integer', 'min:1'],
            'is_featured'   => ['nullable', 'boolean'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $plan->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'monthly_price' => $request->monthly_price,
            'max_employees' => $request->max_employees,
            'is_featured'   => $request->boolean('is_featured'),
            'is_active'     => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Plan updated successfully!');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => false]);
        return back()->with('success', 'Plan deactivated successfully!');
    }

    public function updateDiscounts(Request $request)
    {
        $request->validate([
            'quarterly_discount' => ['required', 'numeric', 'min:0', 'max:100'],
            'biannual_discount'  => ['required', 'numeric', 'min:0', 'max:100'],
            'annual_discount'    => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        SubscriptionDiscount::where('cycle', 'quarterly')->update(['discount_percentage' => $request->quarterly_discount]);
        SubscriptionDiscount::where('cycle', 'biannual')->update(['discount_percentage' => $request->biannual_discount]);
        SubscriptionDiscount::where('cycle', 'annual')->update(['discount_percentage' => $request->annual_discount]);

        return back()->with('success', 'Discounts updated successfully!');
    }
}
