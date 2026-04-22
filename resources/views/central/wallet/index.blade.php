@extends('central.layouts.app')

@section('page-title', 'Wallet Management')
@section('page-subtitle', 'Manage facility wallets and top-up requests')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Total Balance</p>
            <p class="text-2xl font-semibold text-emerald-700">KES {{ number_format($stats['total_balance'], 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Pending Requests</p>
            <p class="text-2xl font-semibold text-amber-600">{{ $stats['pending_requests'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Active Wallets</p>
            <p class="text-2xl font-semibold text-gray-800">{{ $stats['total_wallets'] }}</p>
        </div>
    </div>

    {{-- Pending Requests --}}
    @if($pending->isNotEmpty())
    <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Pending Top-Up Requests</h2>
        <div class="space-y-4">
            @foreach($pending as $req)
            <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $req->tenant->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            KES {{ number_format($req->amount, 2) }} &middot;
                            {{ ucfirst(str_replace('_', ' ', $req->payment_method)) }} &middot;
                            Ref: {{ $req->transaction_reference ?? $req->bank_reference ?? 'N/A' }} &middot;
                            {{ $req->created_at->format('M d, Y h:i A') }}
                        </p>
                        @if($req->proof_file)
                            <a href="{{ asset('wallet-proofs/' . $req->proof_file) }}" target="_blank"
                               class="text-xs text-blue-600 hover:underline mt-1 inline-block">View Proof</a>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.wallets.approve', $req) }}">
                            @csrf
                            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-medium px-4 py-2 rounded-lg">
                                Approve
                            </button>
                        </form>
                        <button onclick="var box = document.getElementById('reject-box-{{ $req->id }}'); box.style.display = box.style.display === 'none' ? 'block' : 'none';"
                                class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium px-4 py-2 rounded-lg">
                            Reject
                        </button>
                    </div>
                </div>
                <div id="reject-box-{{ $req->id }}" style="display:none;" class="mt-2">
                    <form method="POST" action="{{ route('admin.wallets.reject', $req) }}" style="display:flex; gap:8px; width:100%;">
                        @csrf
                        <input type="text" name="rejection_reason" required placeholder="Enter rejection reason"
                               style="flex:1; padding:8px 12px; border:1px solid #fca5a5; border-radius:8px; font-size:13px; min-width:0;"/>
                        <button type="submit"
                                style="background:#dc2626; color:white; border:none; padding:8px 16px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap; flex-shrink:0;">
                            Confirm Reject
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- All Wallets --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-800">All Facility Wallets</h2>
            <a href="{{ route('admin.wallets.requests') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all requests &rarr;</a>
        </div>

        {{-- Manual Credit Form --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <p class="text-xs font-medium text-gray-600 mb-3">Manual Credit</p>
            <form method="POST" action="{{ route('admin.wallets.manual-credit') }}" class="flex gap-3">
                @csrf
                <select name="tenant_id" required class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select Facility</option>
                    @foreach(App\Models\Central\Tenant::all() as $tenant)
                        <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                    @endforeach
                </select>
                <input type="number" name="amount" placeholder="Amount (KES)" min="1" required
                       class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                <input type="text" name="description" placeholder="Description" required
                       class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2 rounded-lg">
                    Credit
                </button>
            </form>
        </div>

        @if($wallets->isEmpty())
            <p class="text-center text-gray-400 text-sm py-8">No wallets yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Facility</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Balance</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Currency</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Last Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($wallets as $wallet)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $wallet->tenant->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold {{ $wallet->balance > 0 ? 'text-emerald-700' : 'text-red-500' }}">
                            KES {{ number_format($wallet->balance, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $wallet->currency }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $wallet->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                {{ $wallet->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $wallet->updated_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $wallets->links() }}</div>
        @endif
    </div>

</div>
@endsection



