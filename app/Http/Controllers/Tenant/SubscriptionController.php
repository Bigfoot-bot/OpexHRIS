<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionDiscount;
use App\Models\FacilitySubscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionInvoice;
use App\Models\SystemSetting;
use App\Models\Central\DarajaSetting;
use App\Models\Central\WalletTopUpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscription = FacilitySubscription::where('tenant_id', tenant('id'))->latest()->first();
        $invoices     = SubscriptionInvoice::where('tenant_id', tenant('id'))->latest()->paginate(10);
        $payments     = SubscriptionPayment::where('tenant_id', tenant('id'))->latest()->take(5)->get();
        return view('tenant.subscription.index', compact('subscription', 'invoices', 'payments'));
    }

    public function plans()
    {
        $allPlans     = SubscriptionPlan::where('is_active', true)->orderBy('monthly_price')->get();
        $discounts    = SubscriptionDiscount::all()->keyBy('cycle');
        $settings     = SystemSetting::getSettings();
        $subscription = FacilitySubscription::where('tenant_id', tenant('id'))
                            ->whereIn('status', ['active', 'trial'])
                            ->latest()->first();

        if ($subscription && $subscription->isActive()) {
            $currentPlan   = $subscription->plan;
            $daysRemaining = $subscription->daysRemaining();
            $canRenewSame  = $daysRemaining <= 7;
            $plans = $allPlans->filter(function($plan) use ($currentPlan, $canRenewSame) {
                // Show plans with strictly higher employee capacity
                if ($plan->max_employees > $currentPlan->max_employees) return true;
                // Allow renewing the same plan within 7 days of expiry
                if ($plan->id === $currentPlan->id && $canRenewSame) return true;
                return false;
            });
            $currentPlanName     = $currentPlan->name;
            $currentMaxEmployees = $currentPlan->max_employees;
            $daysLeft            = $daysRemaining;
            $isHighestPlan       = $plans->isEmpty();
        } else {
            $plans               = $allPlans;
            $currentPlanName     = null;
            $currentMaxEmployees = null;
            $isHighestPlan       = false;
            $daysLeft            = null;
            $canRenewSame        = false;
        }

        return view('tenant.subscription.plans', compact(
            'plans', 'discounts', 'settings', 'currentPlanName', 'currentMaxEmployees',
            'isHighestPlan', 'subscription', 'daysLeft', 'canRenewSame'
        ));
    }

    public function checkoutGet(Request $request)
    {
        $checkoutData = session('checkout_data');
        if (!$checkoutData) {
            return redirect()->route('tenant.subscription.plans');
        }
        $plan      = SubscriptionPlan::findOrFail($checkoutData['plan_id']);
        $discounts = SubscriptionDiscount::all()->keyBy('cycle');
        $settings  = SystemSetting::getSettings();
        $cycle     = $checkoutData['cycle'];
        $discount  = $discounts[$cycle] ?? null;
        $discountPct   = $discount ? (float) $discount->discount_percentage : 0;
        $months        = SubscriptionDiscount::getMonths($cycle);
        $subtotal      = $plan->monthly_price * $months;
        $discountAmt   = $subtotal * ($discountPct / 100);
        $afterDiscount = $subtotal - $discountAmt;
        $vatAmt        = $afterDiscount * ($settings->vat_percentage / 100);
        $total         = $afterDiscount + $vatAmt;
        return view('tenant.subscription.checkout', compact(
            'plan', 'discounts', 'settings', 'discountPct', 'months',
            'subtotal', 'discountAmt', 'afterDiscount', 'vatAmt', 'total'
        ))->with('cycle', $cycle);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'cycle'   => ['required', 'in:monthly,quarterly,biannual,annual'],
        ]);

        $plan      = SubscriptionPlan::findOrFail($request->plan_id);
        $discounts = SubscriptionDiscount::all()->keyBy('cycle');
        $settings  = SystemSetting::getSettings();

        $existing = FacilitySubscription::where('tenant_id', tenant('id'))
                        ->whereIn('status', ['active', 'trial'])
                        ->latest()->first();

        if ($existing && $existing->end_date->isFuture()) {
            $currentPlan   = $existing->plan;
            $daysRemaining = $existing->daysRemaining();
            $isSamePlan    = $plan->id === $currentPlan->id;
            $isLowerPlan   = $plan->max_employees < $currentPlan->max_employees;
            $isSameCapacity = $plan->max_employees === $currentPlan->max_employees && !$isSamePlan;

            if ($isLowerPlan) {
                return redirect()->route('tenant.subscription.plans')
                    ->with('error', "You cannot switch to a plan with fewer employees ({$plan->max_employees}) while your current {$currentPlan->name} plan covers {$currentPlan->max_employees} employees.");
            }
            if ($isSameCapacity) {
                return redirect()->route('tenant.subscription.plans')
                    ->with('error', "This plan covers the same number of employees as your current {$currentPlan->name} plan. Please select a plan with higher employee capacity.");
            }
            if ($isSamePlan && $daysRemaining > 7) {
                return redirect()->route('tenant.subscription.plans')
                    ->with('error', 'Same plan renewal is only available within 7 days of expiry. You have ' . $daysRemaining . ' days remaining.');
            }
        }

        $discount      = $discounts[$request->cycle] ?? null;
        $discountPct   = $discount ? (float) $discount->discount_percentage : 0;
        $months        = SubscriptionDiscount::getMonths($request->cycle);
        $subtotal      = $plan->monthly_price * $months;
        $discountAmt   = $subtotal * ($discountPct / 100);
        $afterDiscount = $subtotal - $discountAmt;
        $vatAmt        = $afterDiscount * ($settings->vat_percentage / 100);
        $total         = $afterDiscount + $vatAmt;

        session(['checkout_data' => ['plan_id' => $plan->id, 'cycle' => $request->cycle]]);

        return view('tenant.subscription.checkout', compact(
            'plan', 'discounts', 'settings', 'discountPct', 'months',
            'subtotal', 'discountAmt', 'afterDiscount', 'vatAmt', 'total'
        ))->with('cycle', $request->cycle);
    }

    public function submitPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id'        => ['required', 'exists:subscription_plans,id'],
            'cycle'          => ['required', 'in:monthly,quarterly,biannual,annual'],
            'payment_method' => ['required', 'in:mpesa_manual,bank_transfer'],
            'amount'         => ['required', 'numeric', 'min:1'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('tenant.subscription.checkout.get')
                ->with('error', $validator->errors()->first());
        }

        $plan        = SubscriptionPlan::findOrFail($request->plan_id);
        $settings    = SystemSetting::getSettings();
        $discountPct = SubscriptionDiscount::getDiscount($request->cycle);
        $months      = SubscriptionDiscount::getMonths($request->cycle);
        $subtotal    = $plan->monthly_price * $months;
        $discountAmt = $subtotal * ($discountPct / 100);
        $afterDisc   = $subtotal - $discountAmt;
        $vatAmt      = $afterDisc * ($settings->vat_percentage / 100);
        $total       = $afterDisc + $vatAmt;

        if ($request->payment_method === 'mpesa_manual') {
            if (empty($request->transaction_reference) || empty($request->mpesa_phone)) {
                return redirect()->route('tenant.subscription.checkout.get')
                    ->with('error', 'Transaction code and phone number are required.');
            }
            if (SubscriptionPayment::isTransactionCodeUsed($request->transaction_reference)) {
                return redirect()->route('tenant.subscription.checkout.get')
                    ->with('error', 'This M-Pesa transaction code has already been used. Please check and try again.');
            }
        }

        if ($request->payment_method === 'bank_transfer') {
            if (empty($request->bank_name) || empty($request->bank_reference)) {
                return redirect()->route('tenant.subscription.checkout.get')
                    ->with('error', 'Bank name and reference are required.');
            }
            if (SubscriptionPayment::isBankReferenceUsed($request->bank_reference)) {
                return redirect()->route('tenant.subscription.checkout.get')
                    ->with('error', 'This bank reference has already been used. Please check and try again.');
            }
        }

        $invoice = SubscriptionInvoice::create([
            'invoice_number'  => SubscriptionInvoice::generateNumber(),
            'tenant_id'       => tenant('id'),
            'plan_id'         => $plan->id,
            'cycle'           => $request->cycle,
            'subtotal'        => $subtotal,
            'discount_amount' => $discountAmt,
            'vat_percentage'  => $settings->vat_percentage,
            'vat_amount'      => $vatAmt,
            'total'           => $total,
            'due_date'        => now()->addDays(7),
            'status'          => 'unpaid',
        ]);

        $paymentData = [
            'tenant_id'       => tenant('id'),
            'plan_id'         => $plan->id,
            'invoice_id'      => $invoice->id,
            'cycle'           => $request->cycle,
            'payment_method'  => $request->payment_method,
            'amount'          => $total,
            'vat_amount'      => $vatAmt,
            'discount_amount' => $discountAmt,
            'status'          => 'pending',
        ];

        if ($request->payment_method === 'mpesa_manual') {
            $paymentData['transaction_reference'] = $request->transaction_reference;
            $paymentData['mpesa_phone']           = $request->mpesa_phone;
        }

        if ($request->payment_method === 'bank_transfer') {
            $paymentData['bank_name']      = $request->bank_name;
            $paymentData['bank_reference'] = $request->bank_reference;
            if ($request->hasFile('proof_file')) {
                $filename = 'sub_proof_' . tenant('id') . '_' . time() . '.' . $request->proof_file->extension();
                $request->proof_file->move(public_path('subscription-proofs'), $filename);
                $paymentData['proof_file'] = $filename;
            }
        }

        SubscriptionPayment::create($paymentData);

        try {
            $tenantData = DB::table('tenants')->where('id', tenant('id'))->first();
            if ($tenantData && $tenantData->email) {
                $pdfPath = \App\Services\InvoicePdfService::generate($invoice, tenant('id'));
                Mail::raw(
                    "Dear {$tenantData->name},\n\nPlease find attached your invoice.\n\nInvoice No: {$invoice->invoice_number}\nPlan: {$plan->name}\nTotal: KES " . number_format($total, 2) . "\n\nYour payment is being reviewed.",
                    function ($m) use ($tenantData, $invoice, $pdfPath) {
                        $m->to($tenantData->email)
                          ->subject("Invoice {$invoice->invoice_number} - OpEx HRIS")
                          ->attach($pdfPath, ['as' => "Invoice-{$invoice->invoice_number}.pdf", 'mime' => 'application/pdf']);
                    }
                );
            }
        } catch (\Exception $e) {
            \Log::error('Invoice email failed: ' . $e->getMessage());
        }

        try {
            $superAdmin = DB::table('super_admins')->first();
            if ($superAdmin) {
                Mail::raw(
                    "New subscription payment submitted by " . tenant('name') . ".\nPlan: {$plan->name}\nAmount: KES " . number_format($total, 2),
                    function ($m) use ($superAdmin) {
                        $m->to($superAdmin->email)->subject('New Subscription Payment - OpEx HRIS');
                    }
                );
            }
        } catch (\Exception $e) {}

        return redirect()->route('tenant.subscription.index')
                         ->with('success', 'Payment submitted! We will activate your subscription once payment is confirmed.');
    }

    public function invoice(SubscriptionInvoice $invoice)
    {
        if ($invoice->tenant_id !== tenant('id')) abort(403);
        $settings = SystemSetting::getSettings();
        $plan     = $invoice->plan;
        return view('tenant.subscription.invoice', compact('invoice', 'settings', 'plan'));
    }

    public function stkPush(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'cycle'   => ['required', 'in:monthly,quarterly,biannual,annual'],
            'amount'  => ['required', 'numeric', 'min:1'],
            'phone'   => ['required', 'string'],
        ]);

        $settings = DarajaSetting::getSettings();

        if (!$settings->is_active) {
            return back()->with('error', 'M-Pesa payments are not configured yet. Please use manual payment.');
        }

        $credentials = base64_encode($settings->consumer_key . ':' . $settings->consumer_secret);
        try {
            $tokenResponse = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Basic ' . $credentials,
            ])->get($settings->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials');
            $accessToken = $tokenResponse->json()['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Subscription STK token error: ' . $e->getMessage());
            $accessToken = null;
        }

        if (!$accessToken) {
            return back()->with('error', 'Failed to connect to M-Pesa. Please try again or use manual payment.');
        }

        $phone     = preg_replace('/^0/', '254', $request->phone);
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($settings->paybill_number . $settings->passkey . $timestamp);
        $amount    = (int) $request->amount;
        $plan      = SubscriptionPlan::findOrFail($request->plan_id);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post($settings->getBaseUrl() . '/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $settings->paybill_number,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => $amount,
                'PartyA'            => $phone,
                'PartyB'            => $settings->paybill_number,
                'PhoneNumber'       => $phone,
                'CallBackURL'       => $settings->callback_url,
                'AccountReference'  => tenant('id'),
                'TransactionDesc'   => 'Subscription - ' . $plan->name . ' - ' . tenant('name'),
            ]);

            $data = $response->json();

            if (isset($data['CheckoutRequestID'])) {
                WalletTopUpRequest::create([
                    'tenant_id'                  => tenant('id'),
                    'payment_method'             => 'mpesa_daraja',
                    'amount'                     => $amount,
                    'mpesa_phone'                => $phone,
                    'status'                     => 'pending',
                    'daraja_checkout_request_id' => $data['CheckoutRequestID'],
                    'plan_id'                    => $request->plan_id,
                    'cycle'                      => $request->cycle,
                ]);

                return back()->with('success', 'M-Pesa prompt sent! Enter your PIN to activate your subscription.');
            }

            return back()->with('error', 'M-Pesa request failed: ' . ($data['errorMessage'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Subscription STK push error: ' . $e->getMessage());
            return back()->with('error', 'M-Pesa request failed. Please try again.');
        }
    }
}
