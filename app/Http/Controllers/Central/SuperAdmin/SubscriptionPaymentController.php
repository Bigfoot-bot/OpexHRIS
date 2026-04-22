<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionInvoice;
use App\Models\FacilitySubscription;
use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use App\Models\SubscriptionDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SubscriptionPaymentController extends Controller
{
    public function index()
    {
        $pending  = SubscriptionPayment::where('status', 'pending')->with('plan')->latest()->get();
        $payments = SubscriptionPayment::with('plan')->latest()->paginate(20);
        return view('central.subscriptions.payments', compact('pending', 'payments'));
    }

    public function approve(Request $request, SubscriptionPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }

        $payment->update([
            'status'      => 'approved',
            'approved_by' => 'Super Admin',
            'approved_at' => now(),
        ]);

        // Update invoice
        $invoice       = SubscriptionInvoice::find($payment->invoice_id);
        $receiptNumber = null;
        if ($invoice) {
            $receiptNumber = SubscriptionInvoice::generateReceiptNumber();
            $invoice->update([
                'status'         => 'paid',
                'receipt_number' => $receiptNumber,
                'paid_at'        => now(),
            ]);
        }

        // Activate/extend subscription
        $months   = SubscriptionDiscount::getMonths($payment->cycle);
        $existing = FacilitySubscription::where('tenant_id', $payment->tenant_id)
                        ->whereIn('status', ['active', 'trial'])
                        ->latest()->first();
        $newPlan  = SubscriptionPlan::find($payment->plan_id);

        if ($existing && $existing->end_date->isFuture()) {
            $currentPlan = SubscriptionPlan::find($existing->plan_id);
            $isUpgrade   = $newPlan && $currentPlan && $newPlan->monthly_price > $currentPlan->monthly_price;

            if ($isUpgrade) {
                // Upgrade to higher plan — starts immediately
                $newEndDate = now()->addMonths($months);
                $existing->update([
                    'plan_id'         => $payment->plan_id,
                    'cycle'           => $payment->cycle,
                    'amount_paid'     => $payment->amount,
                    'vat_amount'      => $payment->vat_amount,
                    'discount_amount' => $payment->discount_amount,
                    'start_date'      => now(),
                    'end_date'        => $newEndDate,
                    'status'          => 'active',
                ]);
            } else {
                // Same plan renewal within 7 days — extend from expiry
                $newEndDate = $existing->end_date->copy()->addMonths($months);
                $existing->update([
                    'end_date' => $newEndDate,
                    'status'   => 'active',
                ]);
            }
        } else {
            // No active subscription — start immediately
            $newEndDate = now()->addMonths($months);
            FacilitySubscription::updateOrCreate(
                ['tenant_id' => $payment->tenant_id],
                [
                    'plan_id'         => $payment->plan_id,
                    'cycle'           => $payment->cycle,
                    'amount_paid'     => $payment->amount,
                    'vat_amount'      => $payment->vat_amount,
                    'discount_amount' => $payment->discount_amount,
                    'start_date'      => now(),
                    'end_date'        => $newEndDate,
                    'status'          => 'active',
                ]
            );
        }

        // Notify tenant with receipt PDF
        try {
            $tenant = DB::table('tenants')->where('id', $payment->tenant_id)->first();
            if ($tenant && $tenant->email) {
                $plan = SubscriptionPlan::find($payment->plan_id);
                $invoice->refresh();
                $pdfPath = \App\Services\InvoicePdfService::generate($invoice, $payment->tenant_id);
                Mail::raw(
                    "Your subscription payment of KES " . number_format($payment->amount, 2) . " has been approved.\n" .
                    "Plan: {$plan->name}\nCycle: " . ucfirst($payment->cycle) . "\nValid until: " . $newEndDate->format('M d, Y') . "\n" .
                    ($receiptNumber ? "Receipt No: {$receiptNumber}" : ''),
                    function ($m) use ($tenant, $invoice, $pdfPath) {
                        $m->to($tenant->email)
                          ->subject('Subscription Payment Approved - OpEx HRIS')
                          ->attach($pdfPath, ['as' => "Receipt-{$invoice->receipt_number}.pdf", 'mime' => 'application/pdf']);
                    }
                );
            }
        } catch (\Exception $e) {}

        // Notify Super Admin
        try {
            $superAdmin = DB::table('super_admins')->first();
            if ($superAdmin) {
                $tenant = DB::table('tenants')->where('id', $payment->tenant_id)->first();
                Mail::raw(
                    "Subscription payment approved for " . ($tenant->name ?? $payment->tenant_id) . ".\nAmount: KES " . number_format($payment->amount, 2),
                    function ($m) use ($superAdmin) {
                        $m->to($superAdmin->email)->subject('Subscription Payment Approved');
                    }
                );
            }
        } catch (\Exception $e) {}

        return back()->with('success', 'Payment approved and subscription activated!');
    }

    public function reject(Request $request, SubscriptionPayment $payment)
    {
        $request->validate(['rejection_reason' => ['required', 'string']]);

        $payment->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        try {
            $tenant = DB::table('tenants')->where('id', $payment->tenant_id)->first();
            if ($tenant && $tenant->email) {
                Mail::raw(
                    "Your subscription payment has been rejected.\nReason: " . $request->rejection_reason,
                    function ($m) use ($tenant) {
                        $m->to($tenant->email)->subject('Subscription Payment Rejected - OpEx HRIS');
                    }
                );
            }
        } catch (\Exception $e) {}

        return back()->with('success', 'Payment rejected.');
    }
}
