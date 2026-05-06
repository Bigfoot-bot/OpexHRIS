@extends('tenant.branch.layout')
@section('page-title', $period->name)
@section('page-subtitle', 'Payroll period detail')
@section('page-actions')
@if($period->status === 'draft')
<form method="POST" action="{{ route('tenant.branch.payroll.approve', [$branch, $period]) }}">
    @csrf
    <button type="submit" onclick="return confirm('Approve payroll for {{ $period->name }}?')"
            class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
        Approve Payroll
    </button>
</form>
@else
<span class="bg-emerald-50 text-emerald-700 text-sm font-medium px-4 py-2 rounded-lg border border-emerald-100">Approved</span>
@endif
@endsection
@section('content')

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl px-4 py-3 mb-6">{{ session('error') }}</div>
@endif

{{-- Summary Cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    @php
        $grossTotal = $period->records->sum('gross_salary');
        $netTotal   = $period->records->sum('net_salary');
        $taxTotal   = $period->records->sum('paye');
        $empCount   = $period->records->count();
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 mb-1">Employees</p>
        <p class="text-2xl font-bold text-gray-800">{{ $empCount }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 mb-1">Gross Pay</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($grossTotal) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 mb-1">Total Tax</p>
        <p class="text-2xl font-bold text-amber-600">{{ number_format($taxTotal) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-400 mb-1">Net Pay</p>
        <p class="text-2xl font-bold text-emerald-700">{{ number_format($netTotal) }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($period->records->isEmpty())
        <div class="p-12 text-center"><p class="text-gray-400 text-sm">No records in this payroll period.</p></div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-4">Basic</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-4">Allowances</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-4">Deductions</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-4">Net Pay</th>
                    @if($period->status === 'draft')
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Edit</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($period->records as $record)
                <tr class="hover:bg-gray-50/50 data-row" data-row-id="{{ $record->id }}">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-800">{{ $record->employee->first_name }} {{ $record->employee->last_name }}</p>
                        <p class="text-xs text-gray-400">{{ $record->employee->job_title ?? $record->employee->department ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-right text-gray-600">{{ number_format($record->basic_salary) }}</td>
                    <td class="px-6 py-4 text-sm text-right text-emerald-600">
                        {{ number_format(($record->house_allowance ?? 0) + ($record->transport_allowance ?? 0) + ($record->medical_allowance ?? 0) + ($record->other_allowances ?? 0) + ($record->overtime_pay ?? 0)) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-right text-red-500">
                        {{ number_format(($record->paye ?? 0) + ($record->nhif ?? 0) + ($record->nssf_employee ?? 0) + ($record->housing_levy ?? 0) + ($record->loan_deduction ?? 0) + ($record->other_deductions ?? 0)) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-semibold text-emerald-700">KES {{ number_format($record->net_salary) }}</td>
                    @if($period->status === 'draft')
                    <td class="px-6 py-4">
                        <button onclick="toggleEditRow({{ $record->id }})" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                    </td>
                    @endif
                </tr>
                @if($period->status === 'draft')
                <tr style="display:none" class="bg-gray-50/80 edit-row" data-edit-id="{{ $record->id }}">
                    <td colspan="6" class="px-6 py-4">
                        <form method="POST" action="{{ route('tenant.branch.payroll.record.update', [$branch, $record]) }}" class="grid grid-cols-4 gap-3">
                            @csrf @method('PUT')
                            <div><label class="text-xs text-gray-500">House Allow.</label><input type="number" name="house_allowance" value="{{ $record->house_allowance }}" step="0.01" class="w-full px-2 py-1.5 border border-gray-200 rounded text-xs mt-1"/></div>
                            <div><label class="text-xs text-gray-500">Transport Allow.</label><input type="number" name="transport_allowance" value="{{ $record->transport_allowance }}" step="0.01" class="w-full px-2 py-1.5 border border-gray-200 rounded text-xs mt-1"/></div>
                            <div><label class="text-xs text-gray-500">Loan Deduction</label><input type="number" name="loan_deduction" value="{{ $record->loan_deduction }}" step="0.01" class="w-full px-2 py-1.5 border border-gray-200 rounded text-xs mt-1"/></div>
                            <div><label class="text-xs text-gray-500">Other Deductions</label><input type="number" name="other_deductions" value="{{ $record->other_deductions }}" step="0.01" class="w-full px-2 py-1.5 border border-gray-200 rounded text-xs mt-1"/></div>
                            <div class="col-span-4"><button type="submit" class="bg-emerald-700 text-white text-xs font-medium px-4 py-1.5 rounded-lg">Update</button></div>
                        </form>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script>
function toggleEditRow(id) {
    var row = document.querySelector('.edit-row[data-edit-id="' + id + '"]');
    if (row) {
        row.style.display = row.style.display === 'none' ? '' : 'none';
    }
}
</script>
@endsection
