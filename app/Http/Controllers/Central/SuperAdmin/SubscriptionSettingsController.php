<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\FacilitySubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SubscriptionSettingsController extends Controller
{
    public function index()
    {
        $settings      = SystemSetting::getSettings();
        $subscriptions = FacilitySubscription::with(['plan'])->latest()->paginate(15);
        return view('central.subscriptions.settings', compact('settings', 'subscriptions'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'vat_percentage'    => ['required', 'numeric', 'min:0', 'max:100'],
            'trial_days'        => ['required', 'integer', 'min:0'],
            'grace_period_days' => ['required', 'integer', 'min:0'],
            'invoice_prefix'    => ['required', 'string', 'max:10'],
            'receipt_prefix'    => ['required', 'string', 'max:10'],
        ]);

        $settings = SystemSetting::getSettings();
        $settings->update($request->only([
            'vat_percentage', 'trial_days', 'grace_period_days',
            'invoice_prefix', 'receipt_prefix',
        ]));

        return back()->with('success', 'Settings updated successfully!');
    }

    public function extendSubscription(Request $request, FacilitySubscription $subscription)
    {
        $days    = intval($request->input('days', 30));
        $newDate = $subscription->end_date->copy()->addDays($days);
        $subscription->update(['end_date' => $newDate, 'status' => 'active']);

        try {
            $tenant = DB::table('tenants')->where('id', $subscription->tenant_id)->first();
            if ($tenant && $tenant->email) {
                Mail::raw(
                    "Dear {$tenant->name},\n\nYour subscription has been extended by {$days} days.\nNew expiry date: " . $newDate->format('M d, Y') . "\n\nThank you.",
                    function ($m) use ($tenant) {
                        $m->to($tenant->email)->subject('Subscription Extended - OpEx HRIS');
                    }
                );
            }
        } catch (\Exception $e) {}

        return back()->with('success', 'Subscription extended by ' . $days . ' days.');
    }

    public function suspendSubscription(FacilitySubscription $subscription)
    {
        $subscription->update(['status' => 'suspended', 'suspended_at' => now()]);

        try {
            $tenant = DB::table('tenants')->where('id', $subscription->tenant_id)->first();
            if ($tenant && $tenant->email) {
                Mail::raw(
                    "Your subscription has been suspended. Please contact support or renew.",
                    function ($m) use ($tenant) {
                        $m->to($tenant->email)->subject('Subscription Suspended - OpEx HRIS');
                    }
                );
            }
        } catch (\Exception $e) {}

        return back()->with('success', 'Subscription suspended.');
    }

    public function cancelSubscription(FacilitySubscription $subscription)
    {
        $subscription->update(['status' => 'cancelled', 'suspended_at' => now()]);

        try {
            $tenant = DB::table('tenants')->where('id', $subscription->tenant_id)->first();
            if ($tenant && $tenant->email) {
                Mail::raw(
                    "Your subscription has been cancelled by the administrator. Please contact support.",
                    function ($m) use ($tenant) {
                        $m->to($tenant->email)->subject('Subscription Cancelled - OpEx HRIS');
                    }
                );
            }
        } catch (\Exception $e) {}

        return back()->with('success', 'Subscription cancelled successfully.');
    }
}

