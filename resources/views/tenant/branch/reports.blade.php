@extends('tenant.branch.layout')
@section('page-title', 'Branch Reports')
@section('page-subtitle', 'Overview of branch statistics')
@section('content')
<div class="space-y-6">

    {{-- Summary Stats --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $totalEmp }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Employees</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $activeEmp }}</p>
            <p class="text-xs text-gray-400 mt-1">Active</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $pendingLeave }}</p>
            <p class="text-xs text-gray-400 mt-1">Pending Leave</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">KES {{ number_format($totalPayroll, 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Payroll</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        {{-- Department Breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Employees by Department</h2>
            @if($deptBreakdown->isEmpty())
                <p class="text-xs text-gray-400">No data available.</p>
            @else
                <div class="space-y-3">
                    @foreach($deptBreakdown as $dept)
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-700">{{ $dept->department ?? 'Unassigned' }}</p>
                        <div class="flex items-center gap-2">
                            <div class="w-24 bg-gray-100 rounded-full h-1.5">
                                <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ $totalEmp > 0 ? ($dept->total / $totalEmp) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-800">{{ $dept->total }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Employment Type Breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Employees by Type</h2>
            @if($typeBreakdown->isEmpty())
                <p class="text-xs text-gray-400">No data available.</p>
            @else
                <div class="space-y-3">
                    @foreach($typeBreakdown as $type)
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $type->employment_type) }}</p>
                        <div class="flex items-center gap-2">
                            <div class="w-24 bg-gray-100 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $totalEmp > 0 ? ($type->total / $totalEmp) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-800">{{ $type->total }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Budget Report --}}
        @if($budget)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 col-span-2">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Budget Report ({{ $budget->period }})</h2>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <p class="text-lg font-bold text-gray-800">KES {{ number_format($budget->allocated_amount, 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Allocated</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-4 text-center">
                    <p class="text-lg font-bold text-amber-600">KES {{ number_format($budget->used_amount, 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Used</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-4 text-center">
                    <p class="text-lg font-bold text-emerald-600">KES {{ number_format($budget->remaining, 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Remaining</p>
                </div>
            </div>
            @php $pct = $budget->allocated_amount > 0 ? ($budget->used_amount / $budget->allocated_amount) * 100 : 0; @endphp
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="bg-emerald-600 h-3 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">{{ number_format($pct, 1) }}% of budget used</p>
        </div>
        @endif
    </div>
</div>
@endsection
