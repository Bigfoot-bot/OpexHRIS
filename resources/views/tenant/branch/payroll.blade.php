@extends('tenant.branch.layout')
@section('page-title', 'Payroll')
@section('page-subtitle', 'Manage branch payroll periods')
@section('page-actions')
<a href="{{ route('tenant.branch.payroll.create', $branch) }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Generate Payroll
</a>
@endsection
@section('content')

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl px-4 py-3 mb-6">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($periods->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-gray-400 text-sm">No payroll periods generated yet.</p>
            <a href="{{ route('tenant.branch.payroll.create', $branch) }}" class="inline-block mt-3 text-sm text-emerald-700 font-medium hover:underline">Generate your first payroll</a>
        </div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Period</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employees</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Total Net Pay</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($periods as $period)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-800">{{ $period->name }}</p>
                        <p class="text-xs text-gray-400">{{ $period->start_date->format('M d') }} – {{ $period->end_date->format('M d, Y') }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $period->records->count() }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-emerald-700">KES {{ number_format($period->records->sum('net_salary'), 2) }}</td>
                    <td class="px-6 py-4">
                        @if($period->status === 'approved')
                            <span class="text-xs bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full">Approved</span>
                        @else
                            <span class="text-xs bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('tenant.branch.payroll.show', [$branch, $period]) }}" class="text-xs text-emerald-700 hover:text-emerald-900 font-medium">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $periods->links() }}</div>
    @endif
</div>
@endsection
