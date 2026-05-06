@extends('tenant.branch.layout')
@section('page-title', $loan->loan_number)
@section('page-subtitle', ($loan->employee->first_name ?? '') . ' ' . ($loan->employee->last_name ?? ''))
@section('content')

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl px-4 py-3 mb-6">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-3 gap-6">
    {{-- Loan Details & Actions --}}
    <div class="col-span-1 space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            @php $lColors = ['pending' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-blue-50 text-blue-600', 'active' => 'bg-emerald-50 text-emerald-700', 'completed' => 'bg-gray-100 text-gray-500', 'rejected' => 'bg-red-50 text-red-600']; @endphp
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-800">Loan Details</h2>
                <span class="text-xs px-2.5 py-1 rounded-full {{ $lColors[$loan->status] ?? '' }} capitalize">{{ $loan->status }}</span>
            </div>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-xs text-gray-400">Type</dt><dd class="text-gray-700 capitalize">{{ str_replace('_', ' ', $loan->type) }}</dd></div>
                <div><dt class="text-xs text-gray-400">Principal</dt><dd class="font-semibold text-gray-800">KES {{ number_format($loan->amount) }}</dd></div>
                <div><dt class="text-xs text-gray-400">Interest Rate</dt><dd class="text-gray-700">{{ $loan->interest_rate }}%</dd></div>
                <div><dt class="text-xs text-gray-400">Total Repayable</dt><dd class="font-medium text-gray-800">KES {{ number_format($loan->total_repayable) }}</dd></div>
                <div><dt class="text-xs text-gray-400">Monthly Payment</dt><dd class="text-gray-700">KES {{ number_format($loan->monthly_deduction) }}</dd></div>
                <div><dt class="text-xs text-gray-400">Repayment Period</dt><dd class="text-gray-700">{{ $loan->repayment_months }} months</dd></div>
                <div><dt class="text-xs text-gray-400">Balance</dt><dd class="font-semibold text-red-600">KES {{ number_format($loan->balance) }}</dd></div>
                @if($loan->disbursement_date)
                <div><dt class="text-xs text-gray-400">Disbursed</dt><dd class="text-gray-700">{{ \Carbon\Carbon::parse($loan->disbursement_date)->format('M d, Y') }}</dd></div>
                @endif
            </dl>
            @if($loan->purpose)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Purpose</p>
                <p class="text-sm text-gray-600">{{ $loan->purpose }}</p>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        @if($loan->status === 'pending')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
            <h2 class="text-sm font-semibold text-gray-800">Actions</h2>
            <form method="POST" action="{{ route('tenant.branch.loans.approve', [$branch, $loan]) }}">
                @csrf
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg">Approve Loan</button>
            </form>
            <form method="POST" action="{{ route('tenant.branch.loans.reject', [$branch, $loan]) }}">
                @csrf
                <button type="submit" onclick="return confirm('Reject this loan application?')" class="w-full bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium py-2 rounded-lg border border-red-100">Reject Loan</button>
            </form>
        </div>
        @elseif($loan->status === 'approved')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">Disburse Loan</h2>
            <p class="text-xs text-gray-500 mb-3">This will mark the loan as active and generate the repayment schedule.</p>
            <form method="POST" action="{{ route('tenant.branch.loans.disburse', [$branch, $loan]) }}">
                @csrf
                <button type="submit" onclick="return confirm('Disburse KES {{ number_format($loan->amount) }} to {{ $loan->employee->first_name }}?')"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg">
                    Disburse KES {{ number_format($loan->amount) }}
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Repayment Schedule --}}
    <div class="col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="text-sm font-semibold text-gray-800">Repayment Schedule</h2>
            </div>
            @if($loan->repayments->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-gray-400 text-sm">{{ $loan->status === 'active' ? 'No repayment records yet.' : 'Repayment schedule will be generated after disbursement.' }}</p>
                </div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">#</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Due Date</th>
                            <th class="text-right text-xs text-gray-400 font-medium px-6 py-3">Amount</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($loan->repayments as $i => $rep)
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($rep->due_date)->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-sm text-right text-gray-700">KES {{ number_format($rep->amount) }}</td>
                            <td class="px-6 py-3">
                                @if($rep->status === 'paid')
                                    <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full">Paid</span>
                                    @if($rep->paid_date)<span class="text-xs text-gray-400 ml-1">{{ \Carbon\Carbon::parse($rep->paid_date)->format('M d') }}</span>@endif
                                @else
                                    <span class="text-xs bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                @if($rep->status === 'pending')
                                <form method="POST" action="{{ route('tenant.branch.loans.payment', [$branch, $rep]) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="date" name="paid_date" value="{{ date('Y-m-d') }}" required
                                           class="px-2 py-1 border border-gray-200 rounded text-xs w-32"/>
                                    <button type="submit" class="text-xs bg-emerald-700 text-white px-2 py-1 rounded font-medium">Mark Paid</button>
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
</div>
@endsection
