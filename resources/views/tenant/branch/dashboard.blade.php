@extends('tenant.branch.layout')
@section('page-title', 'Branch Dashboard')
@section('content')
<div class="space-y-6">

    {{-- Stats --}}
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
            <p class="text-2xl font-bold text-blue-600">{{ $onLeave }}</p>
            <p class="text-xs text-gray-400 mt-1">On Leave</p>
        </div>
    </div>

    {{-- Budget --}}
    @if($budget)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Budget Status ({{ $budget->period }})</h2>
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div><p class="text-xs text-gray-400">Allocated</p><p class="text-lg font-bold text-gray-800">KES {{ number_format($budget->allocated_amount, 0) }}</p></div>
            <div><p class="text-xs text-gray-400">Used</p><p class="text-lg font-bold text-amber-600">KES {{ number_format($budget->used_amount, 0) }}</p></div>
            <div><p class="text-xs text-gray-400">Remaining</p><p class="text-lg font-bold text-emerald-600">KES {{ number_format($budget->remaining, 0) }}</p></div>
        </div>
        @php $pct = $budget->allocated_amount > 0 ? ($budget->used_amount / $budget->allocated_amount) * 100 : 0; @endphp
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1">{{ number_format($pct, 1) }}% used</p>
    </div>
    @endif

    {{-- Recent Employees --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-800">Recent Employees</h2>
            <a href="{{ route('tenant.branch.employees', $branch) }}" class="text-xs text-emerald-600 hover:underline">View all</a>
        </div>
        @if($employees->isEmpty())
            <p class="text-xs text-gray-400">No employees in this branch.</p>
        @else
            <div class="space-y-3">
                @foreach($employees as $emp)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                        <span class="text-xs font-medium text-emerald-700">{{ substr($emp->first_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $emp->first_name }} {{ $emp->last_name }}</p>
                        <p class="text-xs text-gray-400">{{ $emp->job_title ?? 'N/A' }}</p>
                    </div>
                    <span class="ml-auto text-xs px-2 py-0.5 rounded-full {{ $emp->employment_status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">{{ $emp->employment_status }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
