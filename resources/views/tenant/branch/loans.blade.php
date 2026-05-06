@extends('tenant.branch.layout')
@section('page-title', 'Loans')
@section('page-subtitle', 'Manage branch employee loans')
@section('page-actions')
<a href="{{ route('tenant.branch.loans.create', $branch) }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    New Loan
</a>
@endsection
@section('content')

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 mb-1">Pending</p>
        <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 mb-1">Active Loans</p>
        <p class="text-2xl font-bold text-emerald-700">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 mb-1">Total Disbursed</p>
        <p class="text-xl font-bold text-gray-800">{{ number_format($stats['total_disbursed']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 mb-1">Outstanding Balance</p>
        <p class="text-xl font-bold text-red-600">{{ number_format($stats['total_balance']) }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($loans->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <p class="text-gray-400 text-sm">No loans yet.</p>
            <a href="{{ route('tenant.branch.loans.create', $branch) }}" class="inline-block mt-3 text-sm text-emerald-700 font-medium hover:underline">Create first loan</a>
        </div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Loan #</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-4">Amount</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-4">Balance</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($loans as $loan)
                @php $lColors = ['pending' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-blue-50 text-blue-600', 'active' => 'bg-emerald-50 text-emerald-700', 'completed' => 'bg-gray-100 text-gray-500', 'rejected' => 'bg-red-50 text-red-600']; @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $loan->loan_number }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $loan->employee->first_name ?? 'N/A' }} {{ $loan->employee->last_name ?? '' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $loan->type) }}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-700">{{ number_format($loan->amount) }}</td>
                    <td class="px-6 py-4 text-sm text-right font-medium text-red-600">{{ number_format($loan->balance) }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $lColors[$loan->status] ?? '' }} capitalize">{{ $loan->status }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('tenant.branch.loans.show', [$branch, $loan]) }}" class="text-xs text-emerald-700 hover:text-emerald-900 font-medium">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $loans->links() }}</div>
    @endif
</div>
@endsection
