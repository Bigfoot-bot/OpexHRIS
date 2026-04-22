@extends('tenant.employee.layouts.app')

@section('page-title', 'My Dashboard')
@section('page-subtitle', 'Welcome back, ' . $employee->full_name)

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Pending Leave</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['leave_pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting approval</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Approved Leave</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['leave_approved'] }}</p>
            <p class="text-xs text-gray-400 mt-1">This year</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Training Completed</p>
            <p class="text-2xl font-medium text-blue-600">{{ $stats['training'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Programs</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Onboarding</p>
            <p class="text-2xl font-medium text-purple-600">{{ $stats['onboarding'] }}/{{ $stats['onboarding_total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Tasks complete</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-5">

        {{-- Left Column --}}
        <div class="col-span-2 space-y-5">

            {{-- Recent Leave --}}
            <div class="bg-white rounded-xl border border-green-100">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-medium text-emerald-900">My Leave Requests</h2>
                    <a href="{{ route('tenant.employee.leave') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
                </div>
                @if($recentLeave->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-8">No leave requests yet.</p>
                @else
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50">
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Type</th>
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">From</th>
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Days</th>
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentLeave as $leave)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $leave->leaveType->name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $leave->start_date->format('M d, Y') }}</td>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Recent Payslips --}}
            <div class="bg-white rounded-xl border border-green-100">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-medium text-emerald-900">Recent Payslips</h2>
                    <a href="{{ route('tenant.employee.payslips') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
                </div>
                @if($recentPayslips->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-8">No payslips yet.</p>
                @else
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50">
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Period</th>
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Gross</th>
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Deductions</th>
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentPayslips as $payslip)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $payslip->payrollPeriod->name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">KES {{ number_format($payslip->gross_salary) }}</td>
                                <td class="px-6 py-3 text-sm text-red-500">KES {{ number_format($payslip->total_deductions) }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-emerald-900">KES {{ number_format($payslip->net_salary) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Recent Training --}}
            <div class="bg-white rounded-xl border border-green-100">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-medium text-emerald-900">My Training</h2>
                    <a href="{{ route('tenant.employee.training') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
                </div>
                @if($recentTraining->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-8">No training enrolled yet.</p>
                @else
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50">
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Program</th>
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">CPD Points</th>
                                <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentTraining as $enrollment)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $enrollment->trainingProgram->title }}</td>
                                <td class="px-6 py-3 text-sm text-emerald-700">{{ $enrollment->cpd_points_earned }} pts</td>
                                <td class="px-6 py-3">
                                    @php
                                        $colors = [
                                            'enrolled'  => 'bg-blue-50 text-blue-600',
                                            'attended'  => 'bg-amber-50 text-amber-600',
                                            'completed' => 'bg-emerald-50 text-emerald-600',
                                            'cancelled' => 'bg-red-50 text-red-500',
                                        ];
                                    @endphp
                                    <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$enrollment->status] ?? '' }} capitalize">
                                        {{ $enrollment->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>

        {{-- Right Column --}}
        <div class="space-y-5">

            {{-- Profile Card --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-lg font-medium">
                        {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-emerald-900">{{ $employee->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $employee->employee_number }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-400">Job Title</p>
                        <p class="text-sm text-gray-700">{{ $employee->job_title }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Department</p>
                        <p class="text-sm text-gray-700">{{ $employee->department }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Employment Type</p>
                        <p class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $employee->employment_type) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Status</p>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 capitalize">
                            {{ $employee->employment_status }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('tenant.employee.profile') }}"
                   class="block w-full text-center text-sm bg-emerald-50 text-emerald-700 hover:bg-emerald-100 py-2 rounded-lg transition-colors mt-4">
                    View Full Profile
                </a>
            </div>

            {{-- Latest Performance --}}
            @if($latestReview)
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-4">Latest Review</h2>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-400">Period</p>
                        <p class="text-sm text-gray-700">{{ $latestReview->review_period }} {{ $latestReview->review_year }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Rating</p>
                        <p class="text-2xl font-medium text-emerald-900">{{ $latestReview->final_rating ?? '—' }}<span class="text-sm text-gray-400">/5</span></p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <a href="{{ route('tenant.leave-requests.create') }}"
                       class="block w-full text-center text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 py-2 rounded-lg transition-colors">
                        Apply for Leave
                    </a>
                    <a href="{{ route('tenant.employee.payslips') }}"
                       class="block w-full text-center text-sm bg-purple-50 text-purple-700 hover:bg-purple-100 py-2 rounded-lg transition-colors">
                        View Payslips
                    </a>
                    <a href="{{ route('tenant.employee.training') }}"
                       class="block w-full text-center text-sm bg-amber-50 text-amber-700 hover:bg-amber-100 py-2 rounded-lg transition-colors">
                        My Training
                    </a>
                </div>
            </div>

        </div>

    </div>

@endsection