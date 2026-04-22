@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Search Results')
@section('page-subtitle', $query ? 'Results for "' . $query . '"' : 'Search across all modules')

@section('content')

    {{-- Search Bar --}}
    <div class="bg-white rounded-xl border border-green-100 p-5 mb-6">
        <form method="GET" action="{{ route('tenant.search') }}">
            <div class="flex gap-3">
                <input type="text" name="q" value="{{ $query }}" autofocus
                       class="flex-1 px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Search employees, leave requests, payroll, training, positions..."/>
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Search
                </button>
            </div>
        </form>
    </div>

    @if(strlen($query) < 2)
        <div class="bg-white rounded-xl border border-green-100 p-12 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-gray-400 text-sm">Enter at least 2 characters to search</p>
        </div>
    @else
        @php
            $totalResults = $employees->count() + $leaves->count() + $payroll->count() + $training->count() + $positions->count();
        @endphp

        @if($totalResults === 0)
            <div class="bg-white rounded-xl border border-green-100 p-12 text-center">
                <p class="text-gray-400 text-sm">No results found for <strong>{{ $query }}</strong></p>
            </div>
        @else
            <p class="text-xs text-gray-400 mb-4">{{ $totalResults }} result(s) found for "{{ $query }}"</p>

            {{-- Employees --}}
            @if($employees->isNotEmpty())
            <div class="bg-white rounded-xl border border-green-100 mb-4">
                <div class="px-6 py-3 border-b border-gray-50 flex items-center gap-2">
                    <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">Employees</span>
                    <span class="text-xs text-gray-400">{{ $employees->count() }} result(s)</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($employees as $employee)
                    <a href="{{ route('tenant.employees.show', $employee) }}"
                       class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50/50 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-medium flex-shrink-0">
                            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-emerald-900">{{ $employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $employee->employee_number }} · {{ $employee->job_title }} · {{ $employee->department }}</p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full
                            {{ $employee->employment_status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-50 text-gray-500' }} capitalize">
                            {{ $employee->employment_status }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Leave Requests --}}
            @if($leaves->isNotEmpty())
            <div class="bg-white rounded-xl border border-green-100 mb-4">
                <div class="px-6 py-3 border-b border-gray-50 flex items-center gap-2">
                    <span class="text-xs px-2.5 py-1 rounded-full bg-amber-50 text-amber-600">Leave Requests</span>
                    <span class="text-xs text-gray-400">{{ $leaves->count() }} result(s)</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($leaves as $leave)
                    <a href="{{ route('tenant.leave-requests.show', $leave) }}"
                       class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50/50 transition-colors">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-emerald-900">{{ $leave->employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $leave->leaveType->name }} · {{ $leave->start_date->format('M d') }} — {{ $leave->end_date->format('M d, Y') }} · {{ $leave->days_requested }} days</p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full capitalize
                            {{ $leave->status === 'approved' ? 'bg-emerald-50 text-emerald-600' : ($leave->status === 'pending' ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-500') }}">
                            {{ $leave->status }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Payroll --}}
            @if($payroll->isNotEmpty())
            <div class="bg-white rounded-xl border border-green-100 mb-4">
                <div class="px-6 py-3 border-b border-gray-50 flex items-center gap-2">
                    <span class="text-xs px-2.5 py-1 rounded-full bg-blue-50 text-blue-600">Payroll Records</span>
                    <span class="text-xs text-gray-400">{{ $payroll->count() }} result(s)</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($payroll as $record)
                    <div class="flex items-center gap-4 px-6 py-3">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-emerald-900">{{ $record->employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $record->payrollPeriod->name }} · Net: KES {{ number_format($record->net_salary) }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ $record->employee->employee_number }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Training --}}
            @if($training->isNotEmpty())
            <div class="bg-white rounded-xl border border-green-100 mb-4">
                <div class="px-6 py-3 border-b border-gray-50 flex items-center gap-2">
                    <span class="text-xs px-2.5 py-1 rounded-full bg-purple-50 text-purple-600">Training Programs</span>
                    <span class="text-xs text-gray-400">{{ $training->count() }} result(s)</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($training as $program)
                    <a href="{{ route('tenant.training.show', $program) }}"
                       class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50/50 transition-colors">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-emerald-900">{{ $program->title }}</p>
                            <p class="text-xs text-gray-400">{{ $program->type }} · {{ $program->cpd_points }} CPD points</p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-gray-50 text-gray-500 capitalize">
                            {{ $program->status }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Job Positions --}}
            @if($positions->isNotEmpty())
            <div class="bg-white rounded-xl border border-green-100 mb-4">
                <div class="px-6 py-3 border-b border-gray-50 flex items-center gap-2">
                    <span class="text-xs px-2.5 py-1 rounded-full bg-teal-50 text-teal-600">Job Positions</span>
                    <span class="text-xs text-gray-400">{{ $positions->count() }} result(s)</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($positions as $position)
                    <a href="{{ route('tenant.positions.show', $position) }}"
                       class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50/50 transition-colors">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-emerald-900">{{ $position->title }}</p>
                            <p class="text-xs text-gray-400">{{ $position->department }} · {{ $position->employment_type }}</p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full
                            {{ $position->status === 'open' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-50 text-gray-500' }} capitalize">
                            {{ $position->status }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        @endif
    @endif

@endsection

