@extends('tenant.layouts.app')
@section('page-title', 'Expense Claim')
@section('page-subtitle', $expense->claim_number . ' - ' . $expense->title)
@section('page-actions')
    @if($expense->status === 'draft')
        <form method="POST" action="{{ route('tenant.expenses.submit', $expense) }}" class="inline">@csrf<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Submit for Approval</button></form>
    @endif
    @if($expense->status === 'submitted')
        <form method="POST" action="{{ route('tenant.expenses.approve', $expense) }}" class="inline">@csrf<button type="submit" style="background-color:#064e3b;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Approve</button></form>
        <form method="POST" action="{{ route('tenant.expenses.reject', $expense) }}" class="inline">@csrf<button type="submit" style="background-color:#dc2626;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Reject</button></form>
    @endif
    @if($expense->status === 'approved')
        <form method="POST" action="{{ route('tenant.expenses.pay', $expense) }}" class="inline">@csrf<button type="submit" style="background-color:#064e3b;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Pay Now</button></form>
    @endif
    <a href="{{ route('tenant.expenses.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Back</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50"><h2 class="text-sm font-semibold text-gray-800">Expense Items</h2></div>
                <table class="w-full">
                    <thead><tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Category</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Description</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Amount</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Receipt</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($expense->items as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4"><span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 capitalize">{{ $item->category }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item->description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">KES {{ number_format($item->amount, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($item->receipt_path)
                                    <a href="{{ asset($item->receipt_path) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800">View</a>
                                @else
                                    <span class="text-xs text-gray-400">None</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr class="border-t-2 border-gray-200 bg-gray-50">
                            <td colspan="3" class="px-6 py-4 text-sm font-semibold text-gray-800">Total</td>
                            <td class="px-6 py-4 text-sm font-bold text-emerald-700">KES {{ number_format($expense->total_amount, 2) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Claim Details</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Claim No.</span><span class="font-medium text-emerald-700">{{ $expense->claim_number }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Employee</span><span>{{ $expense->employee->first_name ?? 'N/A' }} {{ $expense->employee->last_name ?? '' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Date</span><span>{{ $expense->claim_date->format('M d, Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Status</span>
                        @php $colors = ['draft' => 'text-gray-600', 'submitted' => 'text-amber-600', 'approved' => 'text-emerald-700', 'paid' => 'text-blue-700', 'rejected' => 'text-red-600']; @endphp
                        <span class="font-medium {{ $colors[$expense->status] }} capitalize">{{ $expense->status }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-gray-400">Total</span><span class="font-bold text-gray-800">KES {{ number_format($expense->total_amount, 2) }}</span></div>
                    @if($expense->paid_at)<div class="flex justify-between"><span class="text-gray-400">Paid At</span><span>{{ $expense->paid_at->format('M d, Y') }}</span></div>@endif
                    @if($expense->rejection_reason)<div class="mt-2 p-3 bg-red-50 rounded-lg"><p class="text-xs text-red-600">{{ $expense->rejection_reason }}</p></div>@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





