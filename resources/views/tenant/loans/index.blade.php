@extends('tenant.layouts.app')
@section('page-title', 'Loan Management')
@section('page-subtitle', 'Manage employee loans and advances')
@section('page-actions')
    <a href="{{ route('tenant.loans.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Loan</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['active'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Active</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">KES {{ number_format($stats['total_disbursed'], 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Disbursed</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-red-600">KES {{ number_format($stats['total_balance'], 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Outstanding Balance</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex gap-3">
            <form method="GET" class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
        @if($loans->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No loans yet.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Loan No.</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Amount</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Balance</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Monthly</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($loans as $loan)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm font-medium text-emerald-700">{{ $loan->loan_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $loan->employee->first_name ?? 'N/A' }} {{ $loan->employee->last_name ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $loan->type) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">KES {{ number_format($loan->amount, 0) }}</td>
                        <td class="px-6 py-4 text-sm text-red-600">KES {{ number_format($loan->balance, 0) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">KES {{ number_format($loan->monthly_deduction, 0) }}</td>
                        <td class="px-6 py-4">
                            @php $colors = ['pending' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-blue-50 text-blue-600', 'active' => 'bg-emerald-50 text-emerald-700', 'completed' => 'bg-gray-100 text-gray-600', 'rejected' => 'bg-red-50 text-red-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$loan->status] ?? '' }} capitalize">{{ $loan->status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tenant.loans.show', $loan) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $loans->links() }}</div>
        @endif
    </div>
</div>
@endsection
