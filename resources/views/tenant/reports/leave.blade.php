@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Leave Report')
@section('page-subtitle', 'Leave patterns, approvals and utilization')

@section('page-actions')
    <a href="{{ route('tenant.exports.leave-requests') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        Export Excel
    </a>
    <a href="{{ route('tenant.reports.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Reports
    </a>
@endsection

@section('content')

    {{-- Summary Stats --}}
    <div class="grid grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Requests</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Pending</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Approved</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Rejected</p>
            <p class="text-2xl font-medium text-red-500">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-5 mb-5">

        {{-- By Leave Type --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Leave by Type</h2>
            @if($byType->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">No leave data yet.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium py-2">Type</th>
                            <th class="text-left text-xs text-gray-400 font-medium py-2">Requests</th>
                            <th class="text-left text-xs text-gray-400 font-medium py-2">Total Days</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($byType as $type)
                        <tr>
                            <td class="py-2 text-sm text-gray-700">{{ $type->leaveType->name ?? '—' }}</td>
                            <td class="py-2 text-sm text-gray-600">{{ $type->total }}</td>
                            <td class="py-2 text-sm font-medium text-emerald-900">{{ number_format($type->total_days, 1) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Top Leave Takers --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Top Leave Takers</h2>
            @if($topLeaves->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">No leave data yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($topLeaves as $index => $employee)
                    @if($employee->leave_requests_count > 0)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400 w-5">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm text-emerald-900">{{ $employee->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $employee->department }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-emerald-700">{{ $employee->leave_requests_count }} requests</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Monthly Trend --}}
    <div class="bg-white rounded-xl border border-green-100 p-6">
        <h2 class="text-sm font-medium text-emerald-900 mb-4">Monthly Leave Trend (Last 12 Months)</h2>
        @if($byMonth->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">No leave data yet.</p>
        @else
            <div class="flex items-end gap-2 h-32">
                @php $maxCount = $byMonth->max('total'); @endphp
                @foreach($byMonth as $month)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs text-gray-400">{{ $month->total }}</span>
                    <div class="w-full bg-emerald-100 rounded-t"
                         style="height: {{ $maxCount > 0 ? ($month->total / $maxCount) * 100 : 0 }}px; min-height: 4px;">
                    </div>
                    <span class="text-xs text-gray-400">
                        {{ \Carbon\Carbon::create()->month($month->month)->format('M') }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection
