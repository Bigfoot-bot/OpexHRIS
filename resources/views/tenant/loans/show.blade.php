@extends('tenant.layouts.app')
@section('page-title', 'Loan Details')
@section('page-subtitle', $loan->loan_number)
@section('page-actions')
    @if($loan->status === 'pending')
        <form method="POST" action="{{ route('tenant.loans.approve', $loan) }}" class="inline">@csrf<button type="submit" style="background-color:#064e3b;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Approve</button></form>
        <form method="POST" action="{{ route('tenant.loans.reject', $loan) }}" class="inline">@csrf<button type="submit" style="background-color:#dc2626;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Reject</button></form>
    @endif
    @if($loan->status === 'approved')
        <form method="POST" action="{{ route('tenant.loans.disburse', $loan) }}" class="inline">@csrf<button type="submit" style="background-color:#064e3b;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Disburse Loan</button></form>
    @endif
    <a href="{{ route('tenant.loans.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Back</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 space-y-4">
            {{-- Repayment Schedule --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50"><h2 class="text-sm font-semibold text-gray-800">Repayment Schedule</h2></div>
                @if($loan->repayments->isEmpty())
                    <div class="p-8 text-center"><p class="text-gray-400 text-sm">No repayment schedule yet. Loan must be disbursed first.</p></div>
                @else
                    <table class="w-full">
                        <thead><tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Due Date</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Amount</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Paid Date</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Action</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($loan->repayments as $repayment)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $repayment->due_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">KES {{ number_format($repayment->amount, 0) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $repayment->paid_date ? $repayment->paid_date->format('M d, Y') : '-' }}</td>
                                <td class="px-6 py-4">
                                    @php $colors = ['pending' => 'bg-amber-50 text-amber-600', 'paid' => 'bg-emerald-50 text-emerald-700', 'overdue' => 'bg-red-50 text-red-600']; @endphp
                                    <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$repayment->status] }} capitalize">{{ $repayment->status }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($repayment->status === 'pending')
                                    <form method="POST" action="{{ route('tenant.loans.repayment.pay', $repayment) }}" class="flex gap-2 items-center">
                                        @csrf
                                        <input type="date" name="paid_date" value="{{ date('Y-m-d') }}" class="px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none"/>
                                        <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Mark Paid</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Loan Details</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Loan No.</span><span class="font-medium text-emerald-700">{{ $loan->loan_number }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Employee</span><span>{{ $loan->employee->first_name ?? 'N/A' }} {{ $loan->employee->last_name ?? '' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Type</span><span class="capitalize">{{ str_replace('_', ' ', $loan->type) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Amount</span><span class="font-bold">KES {{ number_format($loan->amount, 0) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Interest</span><span>{{ $loan->interest_rate }}%</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Total Repayable</span><span>KES {{ number_format($loan->total_repayable, 0) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Monthly</span><span class="font-medium text-emerald-700">KES {{ number_format($loan->monthly_deduction, 0) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Balance</span><span class="font-bold text-red-600">KES {{ number_format($loan->balance, 0) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Status</span>
                        @php $colors = ['pending' => 'text-amber-600', 'approved' => 'text-blue-600', 'active' => 'text-emerald-700', 'completed' => 'text-gray-600', 'rejected' => 'text-red-600']; @endphp
                        <span class="font-medium {{ $colors[$loan->status] ?? '' }} capitalize">{{ $loan->status }}</span>
                    </div>
                    @if($loan->disbursement_date)<div class="flex justify-between"><span class="text-gray-400">Disbursed</span><span>{{ $loan->disbursement_date->format('M d, Y') }}</span></div>@endif
                </div>
                @if($loan->purpose)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 mb-1">Purpose</p>
                    <p class="text-sm text-gray-600">{{ $loan->purpose }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
