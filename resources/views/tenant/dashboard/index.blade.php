@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->name)

@section('content')

    {{-- Stats Grid --}}
    <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Employees</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total_employees'] }}</p>
            <p class="text-xs text-emerald-600 mt-1">Registered staff</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Active Staff</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['active_employees'] }}</p>
            <p class="text-xs text-teal-600 mt-1">Currently active</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Pending Leaves</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['pending_leaves'] }}</p>
            <p class="text-xs text-amber-600 mt-1">Awaiting approval</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Expiring Licenses</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['expiring_licenses'] }}</p>
            <p class="text-xs text-amber-600 mt-1">Within 90 days</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Expired Licenses</p>
            <p class="text-2xl font-medium text-red-500">{{ $stats['expired_licenses'] }}</p>
            <p class="text-xs text-red-500 mt-1">Needs attention</p>
        </div>
    </div>

    {{-- Second Stats Row --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Open Positions</p>
            <p class="text-2xl font-medium text-blue-600">{{ $stats['open_positions'] }}</p>
            <p class="text-xs text-blue-600 mt-1">Actively hiring</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Open Cases</p>
            <p class="text-2xl font-medium text-red-500">{{ $stats['open_cases'] }}</p>
            <p class="text-xs text-red-500 mt-1">Disciplinary</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Active Trainings</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['active_trainings'] }}</p>
            <p class="text-xs text-emerald-600 mt-1">Ongoing programs</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Pending Reviews</p>
            <p class="text-2xl font-medium text-purple-600">{{ $stats['pending_reviews'] }}</p>
            <p class="text-xs text-purple-600 mt-1">Performance reviews</p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-3 gap-5 mb-6">

        {{-- Staff by Department --}}
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Staff by Department</h2>
            <div class="relative h-48">
                <canvas id="departmentChart"></canvas>
            </div>
        </div>

        {{-- Leave Status --}}
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Leave Status Distribution</h2>
            <div class="relative h-48">
                <canvas id="leaveChart"></canvas>
            </div>
        </div>

        {{-- Employment Type --}}
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Employment Type</h2>
            <div class="relative h-48">
                <canvas id="employmentChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Payroll Trend --}}
    @if(count($payrollLabels) > 0)
    <div class="bg-white rounded-xl border border-green-100 p-5 mb-6">
        <h2 class="text-sm font-medium text-emerald-900 mb-4">Payroll Trend</h2>
        <div class="relative h-48">
            <canvas id="payrollChart"></canvas>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-3 gap-5">

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('tenant.employees.create') }}"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 transition-colors">
                    <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-600">Add Employee</span>
                </a>
                <a href="{{ route('tenant.leave-requests.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 transition-colors">
                    <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-600">Approve Leave</span>
                </a>
                @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_payroll'))
                <a href="{{ route('tenant.payroll.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 transition-colors">
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-sm text-gray-600">Run Payroll</span>
                </a>
                @endif
                </a>
                <a href="{{ route('tenant.positions.create') }}"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 transition-colors">
                    <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-600">Post Job</span>
                </a>
                <a href="{{ route('tenant.training.create') }}"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 transition-colors">
                    <div class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-600">New Training</span>
                </a>
            </div>
        </div>

        {{-- Pending Leave Requests --}}
        <div class="col-span-2 bg-white rounded-xl border border-green-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-emerald-900">Pending Leave Requests</h2>
                <a href="{{ route('tenant.leave-requests.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
            </div>
            @if($pendingLeaves->isEmpty())
                <div class="flex flex-col items-center justify-center py-8">
                    <p class="text-gray-400 text-sm">No pending leave requests.</p>
                </div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium pb-3">Employee</th>
                            <th class="text-left text-xs text-gray-400 font-medium pb-3">Leave Type</th>
                            <th class="text-left text-xs text-gray-400 font-medium pb-3">Days</th>
                            <th class="text-left text-xs text-gray-400 font-medium pb-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($pendingLeaves as $leave)
                        <tr>
                            <td class="py-3">
                                <p class="text-sm text-emerald-900 font-medium">{{ $leave->employee->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $leave->start_date->format('M d') }} — {{ $leave->end_date->format('M d, Y') }}</p>
                            </td>
                            <td class="py-3 text-sm text-gray-500">{{ $leave->leaveType->name }}</td>
                            <td class="py-3 text-sm text-gray-600">{{ $leave->days_requested }}</td>
                            <td class="py-3">
                                <form method="POST" action="{{ route('tenant.leave-requests.approve', $leave) }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs bg-emerald-50 text-emerald-600 hover:bg-emerald-100 px-3 py-1 rounded-lg transition-colors">
                                        Approve
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-3 gap-5 mt-5">

        {{-- Recent Employees --}}
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-emerald-900">Recent Employees</h2>
                <a href="{{ route('tenant.employees.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
            </div>
            @if($recentEmployees->isEmpty())
                <p class="text-gray-400 text-sm text-center py-6">No employees yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentEmployees as $employee)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-medium flex-shrink-0">
                            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-emerald-900 font-medium truncate">{{ $employee->full_name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $employee->job_title }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'active'     => 'bg-emerald-50 text-emerald-600',
                                'probation'  => 'bg-blue-50 text-blue-600',
                                'suspended'  => 'bg-amber-50 text-amber-600',
                                'terminated' => 'bg-red-50 text-red-500',
                                'resigned'   => 'bg-gray-50 text-gray-500',
                            ];
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$employee->employment_status] ?? '' }} capitalize flex-shrink-0">
                            {{ $employee->employment_status }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Latest Payroll --}}
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-emerald-900">Latest Payroll</h2>
                <a href="{{ route('tenant.payroll.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
            </div>
            @if($recentPayroll)
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400">Period</p>
                        <p class="text-sm font-medium text-emerald-900">{{ $recentPayroll->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Gross Payroll</p>
                        <p class="text-sm text-gray-700">KES {{ number_format($recentPayroll->records->sum('gross_salary')) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Net Payroll</p>
                        <p class="text-lg font-medium text-emerald-900">KES {{ number_format($recentPayroll->records->sum('net_salary')) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Status</p>
                        @php
                            $payrollStatusColors = [
                                'draft'    => 'bg-gray-50 text-gray-500',
                                'approved' => 'bg-emerald-50 text-emerald-600',
                                'paid'     => 'bg-teal-50 text-teal-600',
                            ];
                        @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $payrollStatusColors[$recentPayroll->status] ?? '' }} capitalize">
                            {{ $recentPayroll->status }}
                        </span>
                    </div>
                    <a href="{{ route('tenant.payroll.show', $recentPayroll) }}"
                       class="block text-center text-xs bg-emerald-50 text-emerald-700 hover:bg-emerald-100 py-2 rounded-lg transition-colors mt-2">
                        View Payroll
                    </a>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-8">
                    <p class="text-gray-400 text-sm">No payroll run yet.</p>
                    <a href="{{ route('tenant.payroll.create') }}" class="text-xs text-emerald-600 mt-2">Run payroll →</a>
                </div>
            @endif
        </div>

        {{-- Upcoming Training --}}
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-emerald-900">Upcoming Training</h2>
                <a href="{{ route('tenant.training.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
            </div>
            @if($upcomingTraining->isEmpty())
                <div class="flex flex-col items-center justify-center py-8">
                    <p class="text-gray-400 text-sm">No upcoming training.</p>
                    <a href="{{ route('tenant.training.create') }}" class="text-xs text-emerald-600 mt-2">Schedule training →</a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($upcomingTraining as $training)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-emerald-900">{{ $training->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $training->start_date->format('M d, Y') }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-emerald-600">{{ $training->cpd_points }} CPD pts</span>
                            <span class="text-xs text-gray-400 capitalize">{{ $training->type }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const chartDefaults = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, padding: 10 }
                }
            }
        };

        // 1. Staff by Department - Bar Chart
        new Chart(document.getElementById('departmentChart'), {
            type: 'bar',
            data: {
                labels: @json($departmentLabels),
                datasets: [{
                    label: 'Staff',
                    data: @json($departmentData),
                    backgroundColor: '#059669',
                    borderRadius: 6,
                }]
            },
            options: {
                ...chartDefaults,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Leave Status - Doughnut Chart
        new Chart(document.getElementById('leaveChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Approved', 'Rejected'],
                datasets: [{
                    data: @json($leaveStatusData),
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                    borderWidth: 0,
                }]
            },
            options: { ...chartDefaults, cutout: '65%' }
        });

        // 3. Employment Type - Doughnut Chart
        new Chart(document.getElementById('employmentChart'), {
            type: 'doughnut',
            data: {
                labels: @json($employmentTypeLabels),
                datasets: [{
                    data: @json($employmentTypeData),
                    backgroundColor: ['#059669', '#3b82f6', '#8b5cf6', '#f59e0b'],
                    borderWidth: 0,
                }]
            },
            options: { ...chartDefaults, cutout: '65%' }
        });

        // 4. Payroll Trend - Line Chart
        @if(count($payrollLabels) > 0)
        new Chart(document.getElementById('payrollChart'), {
            type: 'line',
            data: {
                labels: @json($payrollLabels),
                datasets: [
                    {
                        label: 'Gross',
                        data: @json($payrollGrossData),
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5,150,105,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                    },
                    {
                        label: 'Net',
                        data: @json($payrollNetData),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                    }
                ]
            },
            options: {
                ...chartDefaults,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 } } }
                },
                scales: {
                    y: { beginAtZero: false },
                    x: { grid: { display: false } }
                }
            }
        });
        @endif
    </script>

@endsection
