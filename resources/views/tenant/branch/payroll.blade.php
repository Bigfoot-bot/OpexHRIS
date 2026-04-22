@extends('tenant.branch.layout')
@section('page-title', 'Branch Payroll')
@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($records->isEmpty())
        <div class="p-12 text-center"><p class="text-gray-400 text-sm">No payroll records found for this branch.</p></div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Period</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Gross</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Net Pay</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($records as $record)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $record->employee->first_name ?? 'N/A' }} {{ $record->employee->last_name ?? '' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $record->period->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">KES {{ number_format($record->gross_salary, 2) }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-emerald-700">KES {{ number_format($record->net_salary, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $records->links() }}</div>
    @endif
</div>
@endsection


