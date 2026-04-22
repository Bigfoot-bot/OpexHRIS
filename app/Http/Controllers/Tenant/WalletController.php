<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Central\FacilityWallet;
use App\Models\Central\WalletTopUpRequest;
use App\Models\Central\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WalletController extends Controller
{
    public function index()
    {
        $wallet       = FacilityWallet::getOrCreate(tenant('id'));
        $transactions = WalletTransaction::where('tenant_id', tenant('id'))->latest()->paginate(15);
        $requests     = WalletTopUpRequest::where('tenant_id', tenant('id'))->latest()->take(5)->get();
        return view('tenant.wallet.index', compact('wallet', 'transactions', 'requests'));
    }

    public function topUp()
    {
        $wallet   = FacilityWallet::getOrCreate(tenant('id'));
        $settings = \App\Models\Central\DarajaSetting::getSettings();
        return view('tenant.wallet.top-up', compact('wallet', 'settings'));
    }

    public function submitTopUp(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', 'in:mpesa_manual,bank_transfer'],
            'amount'         => ['required', 'numeric', 'min:100'],
        ]);

        $data = [
            'tenant_id'      => tenant('id'),
            'payment_method' => $request->payment_method,
            'amount'         => $request->amount,
            'status'         => 'pending',
        ];

        if ($request->payment_method === 'mpesa_manual') {
            $request->validate([
                'transaction_reference' => ['required', 'string'],
                'mpesa_phone'           => ['required', 'string'],
            ]);

            // Cross-check against all payment references
            $used = \DB::table('wallet_top_up_requests')->where('transaction_reference', $request->transaction_reference)->whereIn('status', ['pending', 'approved'])->exists()
                 || \DB::table('wallet_top_up_requests')->where('bank_reference', $request->transaction_reference)->whereIn('status', ['pending', 'approved'])->exists()
                 || \App\Models\SubscriptionPayment::where('transaction_reference', $request->transaction_reference)->whereIn('status', ['pending', 'approved'])->exists()
                 || \App\Models\SubscriptionPayment::where('bank_reference', $request->transaction_reference)->whereIn('status', ['pending', 'approved'])->exists();

            if ($used) {
                return back()->withErrors([
                    'transaction_reference' => 'This M-Pesa transaction code has already been used. Please check and try again.'
                ])->withInput();
            }

            $data['transaction_reference'] = $request->transaction_reference;
            $data['mpesa_phone']           = $request->mpesa_phone;
        }

        if ($request->payment_method === 'bank_transfer') {
            $request->validate([
                'bank_name'      => ['required', 'string'],
                'bank_reference' => ['required', 'string'],
            ]);

            // Cross-check against all payment references
            $used = \DB::table('wallet_top_up_requests')->where('bank_reference', $request->bank_reference)->whereIn('status', ['pending', 'approved'])->exists()
                 || \DB::table('wallet_top_up_requests')->where('transaction_reference', $request->bank_reference)->whereIn('status', ['pending', 'approved'])->exists()
                 || \App\Models\SubscriptionPayment::where('bank_reference', $request->bank_reference)->whereIn('status', ['pending', 'approved'])->exists()
                 || \App\Models\SubscriptionPayment::where('transaction_reference', $request->bank_reference)->whereIn('status', ['pending', 'approved'])->exists();

            if ($used) {
                return back()->withErrors([
                    'bank_reference' => 'This bank transaction reference has already been used. Please check and try again.'
                ])->withInput();
            }

            $data['bank_name']      = $request->bank_name;
            $data['bank_reference'] = $request->bank_reference;

            if ($request->hasFile('proof_file')) {
                $filename = 'proof_' . tenant('id') . '_' . time() . '.' . $request->proof_file->extension();
                $request->proof_file->move(public_path('wallet-proofs'), $filename);
                $data['proof_file'] = $filename;
            }
        }

        WalletTopUpRequest::create($data);

        // Notify Super Admin
        try {
            $superAdmin = \DB::connection('mysql')->table('super_admins')->first();
            if ($superAdmin) {
                $tenantName = tenant('name');
                $method     = ucfirst(str_replace('_', ' ', $request->payment_method));
                Mail::raw(
                    "New wallet top-up request from {$tenantName}.\nAmount: KES " . number_format($request->amount, 2) . "\nMethod: {$method}\nReference: " . ($data['transaction_reference'] ?? $data['bank_reference'] ?? 'N/A'),
                    function ($m) use ($superAdmin, $tenantName) {
                        $m->to($superAdmin->email)->subject("New Wallet Top-Up Request - {$tenantName}");
                    }
                );
            }
        } catch (\Exception $e) {
            \Log::error('Wallet top-up notification failed: ' . $e->getMessage());
        }

        return redirect()->route('tenant.wallet.index')
                         ->with('success', 'Top-up request submitted! We will credit your wallet once payment is confirmed.');
    }
}
