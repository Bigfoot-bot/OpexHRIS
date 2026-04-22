@extends('tenant.employee.layouts.app')
@section('page-title', 'My Leave')
@section('page-subtitle', 'Your leave requests and history')

@section('page-actions')
    <a href="{{ route('tenant.leave-requests.create') }}"
       class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        + Apply for Leave
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Leave Balances --}}
    @if($leaveBalances->isNotEmpty())
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-widest mb-3">{{ now()->year }} Leave Balances</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($leaveBalances as $balance)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-medium text-gray-500">{{ $balance->leaveType->name }}</p>
                    <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full">{{ $balance->allocated_days }} days</span>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-2xl font-semibold text-gray-800">{{ $balance->remaining_days }}</p>
                        <p class="text-xs text-gray-400">days remaining</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-red-400">{{ $balance->used_days }}</p>
                        <p class="text-xs text-gray-400">days used</p>
                    </div>
                </div>
                {{-- Progress bar --}}
                <div class="mt-3 bg-gray-100 rounded-full h-1.5">
                    @php
                        $percent = $balance->allocated_days > 0
                            ? ($balance->used_days / $balance->allocated_days) * 100
                            : 0;
                    @endphp
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, $percent) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Leave Requests --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-widest mb-3">Leave History</h2>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            @if($leaveRequests->isEmpty())
                <div class="text-center py-16">
                    <p class="text-gray-400 text-sm">No leave requests yet.</p>
                    <a href="{{ route('tenant.leave-requests.create') }}"
                       class="inline-block mt-3 text-sm text-blue-600 hover:text-blue-800">
                        Apply for leave &rarr;
                    </a>
                </div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">From</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">To</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Days</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($leaveRequests as $leave)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $leave->leaveType->name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $leave->start_date->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $leave->end_date->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $leave->days_requested }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $colors = [
                                        'pending'  => 'bg-amber-50 text-amber-600',
                                        'approved' => 'bg-emerald-50 text-emerald-600',
                                        'rejected' => 'bg-red-50 text-red-500',
                                    ];
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$leave->status] ?? '' }} capitalize">
                                    {{ $leave->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-400">{{ $leave->reason ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($leaveRequests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-50">
                        {{ $leaveRequests->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>
@endsection
