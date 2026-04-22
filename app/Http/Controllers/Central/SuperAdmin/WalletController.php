<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Central\FacilityWallet;
use App\Models\Central\WalletTopUpRequest;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WalletController extends Controller
{
    public function index()
    {
        $wallets  = FacilityWallet::with('tenant')->latest()->paginate(15);
        $pending  = WalletTopUpRequest::where('status', 'pending')->with('tenant')->latest()->get();
        $stats = [
            'total_balance'   => FacilityWallet::sum('balance'),
            'pending_requests'=> WalletTopUpRequest::where('status', 'pending')->count(),
            'total_wallets'   => FacilityWallet::count(),
        ];
        return view('central.wallet.index', compact('wallets', 'pending', 'stats'));
    }

    public function requests()
    {
        $requests = WalletTopUpRequest::with('tenant')->latest()->paginate(20);
        return view('central.wallet.requests', compact('requests'));
    }

    public function approve(Request $request, WalletTopUpRequest $topUpRequest)
    {
        if (!$topUpRequest->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        $wallet = FacilityWallet::getOrCreate($topUpRequest->tenant_id);
        $wallet->credit(
            $topUpRequest->amount,
            'Wallet top-up approved - ' . ucfirst(str_replace('_', ' ', $topUpRequest->payment_method)),
            $topUpRequest->payment_method,
            $topUpRequest->transaction_reference ?? $topUpRequest->bank_reference,
            'super_admin'
        );

        $topUpRequest->update([
            'status'      => 'approved',
            'approved_by' => 'Super Admin',
            'approved_at' => now(),
        ]);

        // Notify tenant admin
        try {
            $tenant = $topUpRequest->tenant;
            if ($tenant && $tenant->email) {
                Mail::raw(
                    "Your wallet top-up request of KES " . number_format($topUpRequest->amount, 2) . " has been approved. Your new balance is KES " . number_format($wallet->balance, 2),
                    function ($m) use ($tenant) {
                        $m->to($tenant->email)->subject('Wallet Top-Up Approved - OpEx HRIS');
                    }
                );
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return back()->with('success', 'Wallet credited successfully! KES ' . number_format($topUpRequest->amount, 2) . ' added.');
    }

    public function reject(Request $request, WalletTopUpRequest $topUpRequest)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string'],
        ]);

        $topUpRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notify tenant admin
        try {
            $tenant = $topUpRequest->tenant;
            if ($tenant && $tenant->email) {
                Mail::raw(
                    "Your wallet top-up request of KES " . number_format($topUpRequest->amount, 2) . " has been rejected. Reason: " . $request->rejection_reason,
                    function ($m) use ($tenant) {
                        $m->to($tenant->email)->subject('Wallet Top-Up Rejected - OpEx HRIS');
                    }
                );
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return back()->with('success', 'Request rejected.');
    }

    public function manualCredit(Request $request)
    {
        $request->validate([
            'tenant_id'   => ['required', 'exists:tenants,id'],
            'amount'      => ['required', 'numeric', 'min:1'],
            'description' => ['required', 'string'],
        ]);

        $wallet = FacilityWallet::getOrCreate($request->tenant_id);
        $wallet->credit(
            $request->amount,
            $request->description,
            'adjustment',
            null,
            'super_admin'
        );

        return back()->with('success', 'KES ' . number_format($request->amount, 2) . ' credited to wallet.');
    }
}
