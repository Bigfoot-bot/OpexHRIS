@extends('central.layouts.app')

@section('page-title', 'Subscription Payments')
@section('page-subtitle', 'Review and approve subscription payments')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- Pending Payments --}}
    @if($pending->isNotEmpty())
    <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Pending Payments</h2>
        <div class="space-y-4">
            @foreach($pending as $payment)
            @php $tenant = DB::table('tenants')->where('id', $payment->tenant_id)->first(); @endphp
            <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $tenant->name ?? $payment->tenant_id }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $payment->plan->name ?? 'N/A' }} &middot;
                            {{ ucfirst($payment->cycle) }} &middot;
                            KES {{ number_format($payment->amount, 2) }} &middot;
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} &middot;
                            Ref: {{ $payment->transaction_reference ?? $payment->bank_reference ?? 'N/A' }} &middot;
                            {{ $payment->created_at->format('M d, Y h:i A') }}
                        </p>
                        @if($payment->proof_file)
                            <a href="{{ asset('subscription-proofs/' . $payment->proof_file) }}" target="_blank"
                               class="text-xs text-blue-600 hover:underline mt-1 inline-block">View Proof</a>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.subscription-payments.approve', $payment) }}">
                            @csrf
                            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-medium px-4 py-2 rounded-lg">
                                Approve
                            </button>
                        </form>
                        <button onclick="document.getElementById('reject-{{ $payment->id }}').style.display='block'"
                                class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium px-4 py-2 rounded-lg">
                            Reject
                        </button>
                    </div>
                </div>
                <div id="reject-{{ $payment->id }}" style="display:none;" class="mt-2">
                    <form method="POST" action="{{ route('admin.subscription-payments.reject', $payment) }}" style="display:flex;gap:8px;">
                        @csrf
                        <input type="text" name="rejection_reason" required placeholder="Rejection reason"
                               style="flex:1;padding:8px 12px;border:1px solid #fca5a5;border-radius:8px;font-size:13px;"/>
                        <button type="submit" style="background:#dc2626;color:white;border:none;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">
                            Confirm Reject
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- All Payments --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">All Payments</h2>
        @if($payments->isEmpty())
            <p class="text-center text-gray-400 text-sm py-8">No payments yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Facility</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Plan</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Amount</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Method</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payments as $payment)
                    @php $tenant = DB::table('tenants')->where('id', $payment->tenant_id)->first(); @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $tenant->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $payment->plan->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-emerald-700">KES {{ number_format($payment->amount, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="px-4 py-3">
                            @php $colors = ['pending' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-red-50 text-red-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$payment->status] ?? '' }} capitalize">{{ $payment->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $payment->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $payments->links() }}</div>
        @endif
    </div>

</div>
@endsection
