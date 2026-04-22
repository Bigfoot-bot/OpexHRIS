@extends('central.layouts.app')

@section('page-title', 'Top-Up Requests')
@section('page-subtitle', 'All wallet top-up requests from facilities')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 m-4">{{ session('success') }}</div>
    @endif

    @if($requests->isEmpty())
        <div class="text-center py-16">
            <p class="text-gray-400 text-sm">No top-up requests yet.</p>
        </div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Facility</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Amount</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Method</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Reference</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Date</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($requests as $req)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $req->tenant->name ?? 'N/A' }}</td>
                    <td class="px-6 py-3 text-sm font-semibold text-emerald-700">KES {{ number_format($req->amount, 2) }}</td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $req->payment_method)) }}</td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $req->transaction_reference ?? $req->bank_reference ?? 'N/A' }}</td>
                    <td class="px-6 py-3">
                        @php
                            $colors = ['pending' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-red-50 text-red-600'];
                        @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$req->status] ?? '' }} capitalize">{{ $req->status }}</span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-400">{{ $req->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-3">
                        @if($req->isPending())
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.wallets.approve', $req) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.wallets.reject', $req) }}">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" value="Request rejected by admin"/>
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Reject</button>
                                </form>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">{{ $req->approved_by ?? '-' }}</span>
                        @endif
                        @if($req->proof_file)
                            <a href="{{ asset('wallet-proofs/' . $req->proof_file) }}" target="_blank"
                               class="text-xs text-blue-500 hover:underline block mt-1">View Proof</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-50">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
