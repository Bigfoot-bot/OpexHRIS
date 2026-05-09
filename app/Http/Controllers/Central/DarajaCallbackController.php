<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\FacilityWallet;
use App\Models\Central\WalletTopUpRequest;
use App\Models\FacilitySubscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionDiscount;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DarajaCallbackController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Daraja callback received', $request->all());

        $data = $request->json()->all();

        try {
            $resultCode       = $data['Body']['stkCallback']['ResultCode'] ?? null;
            $checkoutId       = $data['Body']['stkCallback']['CheckoutRequestID'] ?? null;
            $callbackMetadata = $data['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];

            $topUpRequest = WalletTopUpRequest::where('daraja_checkout_request_id', $checkoutId)->first();

            if (!$topUpRequest) {
                Log::error('Daraja callback: top-up request not found for checkout ID: ' . $checkoutId);
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
            }

            if ($resultCode === 0) {
                $mpesaRef = collect($callbackMetadata)->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;
                $amount   = collect($callbackMetadata)->firstWhere('Name', 'Amount')['Value'] ?? $topUpRequest->amount;

                $topUpRequest->update([
                    'status'                => 'approved',
                    'transaction_reference' => $mpesaRef,
                    'approved_at'           => now(),
                    'approved_by'           => 'Daraja Auto',
                ]);

                // If this STK push was for a subscription, activate it — do NOT credit wallet
                if ($topUpRequest->plan_id && $topUpRequest->cycle) {
                    $this->activateSubscription($topUpRequest, $amount, $mpesaRef);
                } else {
                    $wallet = FacilityWallet::getOrCreate($topUpRequest->tenant_id);
                    $wallet->credit(
                        $amount,
                        'M-Pesa top-up via Daraja - ' . $mpesaRef,
                        'mpesa_daraja',
                        $mpesaRef,
                        'daraja_auto'
                    );
                    Log::info('Daraja callback: wallet credited', ['tenant' => $topUpRequest->tenant_id, 'amount' => $amount, 'ref' => $mpesaRef]);
                }
            } else {
                $topUpRequest->update(['status' => 'rejected', 'rejection_reason' => 'M-Pesa payment failed or cancelled']);
                Log::info('Daraja callback: payment failed/cancelled', ['checkout_id' => $checkoutId, 'result_code' => $resultCode]);
            }
        } catch (\Exception $e) {
            Log::error('Daraja callback error: ' . $e->getMessage());
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    private function activateSubscription(WalletTopUpRequest $topUpRequest, float $amount, ?string $mpesaRef): void
    {
        $plan = SubscriptionPlan::find($topUpRequest->plan_id);
        if (!$plan) {
            Log::error('Daraja callback: plan not found', ['plan_id' => $topUpRequest->plan_id]);
            return;
        }

        $settings    = SystemSetting::getSettings();
        $discountPct = SubscriptionDiscount::getDiscount($topUpRequest->cycle);
        $months      = SubscriptionDiscount::getMonths($topUpRequest->cycle);
        $subtotal    = $plan->monthly_price * $months;
        $discountAmt = $subtotal * ($discountPct / 100);
        $afterDisc   = $subtotal - $discountAmt;
        $vatAmt      = $afterDisc * ($settings->vat_percentage / 100);

        $startDate = now()->toDateString();
        $endDate   = now()->addMonths($months)->toDateString();

        FacilitySubscription::create([
            'tenant_id'       => $topUpRequest->tenant_id,
            'plan_id'         => $plan->id,
            'cycle'           => $topUpRequest->cycle,
            'amount_paid'     => $amount,
            'vat_amount'      => $vatAmt,
            'discount_amount' => $discountAmt,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'status'          => 'active',
            'auto_renew'      => false,
        ]);

        Log::info('Daraja callback: subscription activated', [
            'tenant'   => $topUpRequest->tenant_id,
            'plan'     => $plan->name,
            'cycle'    => $topUpRequest->cycle,
            'end_date' => $endDate,
            'ref'      => $mpesaRef,
        ]);
    }
}
