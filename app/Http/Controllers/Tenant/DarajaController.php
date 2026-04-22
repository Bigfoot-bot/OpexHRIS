<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Central\DarajaSetting;
use App\Models\Central\FacilityWallet;
use App\Models\Central\WalletTopUpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DarajaController extends Controller
{
    private function getAccessToken(DarajaSetting $settings): ?string
    {
        try {
            $credentials = base64_encode($settings->consumer_key . ':' . $settings->consumer_secret);
            $response    = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Basic ' . $credentials,
            ])->get($settings->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials');

            return $response->json()['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Daraja access token error: ' . $e->getMessage());
            return null;
        }
    }

    public function stkPush(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
            'phone'  => ['required', 'string'],
        ]);

        $settings = DarajaSetting::getSettings();

        if (!$settings->is_active) {
            return back()->with('error', 'M-Pesa payments are not configured yet. Please use manual payment.');
        }

        $accessToken = $this->getAccessToken($settings);
        if (!$accessToken) {
            return back()->with('error', 'Failed to connect to M-Pesa. Please try again or use manual payment.');
        }

        $phone     = preg_replace('/^0/', '254', $request->phone);
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($settings->paybill_number . $settings->passkey . $timestamp);
        $amount    = (int) $request->amount;

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
                'TransactionDesc'   => 'HRIS Wallet Top-Up - ' . tenant('name'),
            ]);

            $data = $response->json();

            if (isset($data['CheckoutRequestID'])) {
                // Save pending request
                WalletTopUpRequest::create([
                    'tenant_id'                  => tenant('id'),
                    'payment_method'             => 'mpesa_daraja',
                    'amount'                     => $amount,
                    'mpesa_phone'                => $phone,
                    'status'                     => 'pending',
                    'daraja_checkout_request_id' => $data['CheckoutRequestID'],
                ]);

                return back()->with('success', 'M-Pesa payment request sent! Please check your phone and enter your PIN.');
            }

            return back()->with('error', 'M-Pesa request failed: ' . ($data['errorMessage'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('STK Push error: ' . $e->getMessage());
            return back()->with('error', 'M-Pesa request failed. Please try again.');
        }
    }

    public function callback(Request $request)
    {
        Log::info('Daraja callback received', $request->all());

        $data = $request->json()->all();

        try {
            $resultCode       = $data['Body']['stkCallback']['ResultCode'] ?? null;
            $checkoutId       = $data['Body']['stkCallback']['CheckoutRequestID'] ?? null;
            $callbackMetadata = $data['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];

            $topUpRequest = WalletTopUpRequest::where('daraja_checkout_request_id', $checkoutId)->first();

            if (!$topUpRequest) {
                Log::error('Top-up request not found for checkout ID: ' . $checkoutId);
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
            }

            if ($resultCode === 0) {
                // Payment successful
                $mpesaRef = collect($callbackMetadata)->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;
                $amount   = collect($callbackMetadata)->firstWhere('Name', 'Amount')['Value'] ?? $topUpRequest->amount;

                $topUpRequest->update([
                    'status'                => 'approved',
                    'transaction_reference' => $mpesaRef,
                    'approved_at'           => now(),
                    'approved_by'           => 'Daraja Auto',
                ]);

                // Credit wallet automatically
                $wallet = FacilityWallet::getOrCreate($topUpRequest->tenant_id);
                $wallet->credit(
                    $amount,
                    'M-Pesa top-up via Daraja - ' . $mpesaRef,
                    'mpesa_daraja',
                    $mpesaRef,
                    'daraja_auto'
                );

                Log::info('Wallet credited automatically via Daraja', ['tenant' => $topUpRequest->tenant_id, 'amount' => $amount]);
            } else {
                // Payment failed
                $topUpRequest->update(['status' => 'rejected', 'rejection_reason' => 'M-Pesa payment failed or cancelled']);
                Log::info('Daraja payment failed', ['checkout_id' => $checkoutId, 'result_code' => $resultCode]);
            }
        } catch (\Exception $e) {
            Log::error('Daraja callback error: ' . $e->getMessage());
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }
}

