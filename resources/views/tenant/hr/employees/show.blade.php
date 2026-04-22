@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', $employee->full_name)
@section('page-subtitle', $employee->job_title . ' — ' . $employee->department)

@section('page-actions')
    <a href="{{ route('tenant.employees.edit', $employee) }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        Edit Employee
    </a>
    <a href="{{ route('tenant.employees.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

<div class="grid grid-cols-3 gap-5">

    {{-- Left Column --}}
    <div class="col-span-2 space-y-5">

        {{-- Personal Info --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-lg font-medium">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-base font-medium text-emerald-900">{{ $employee->full_name }}</h2>
                    <p class="text-sm text-gray-400">{{ $employee->employee_number }} · {{ $employee->job_title }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Email</p>
                    <p class="text-sm text-gray-700">{{ $employee->email ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Phone</p>
                    <p class="text-sm text-gray-700">{{ $employee->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Gender</p>
                    <p class="text-sm text-gray-700 capitalize">{{ $employee->gender ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Date of Birth</p>
                    <p class="text-sm text-gray-700">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">National ID</p>
                    <p class="text-sm text-gray-700">{{ $employee->national_id ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">KRA PIN</p>
                    <p class="text-sm text-gray-700">{{ $employee->kra_pin ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">NHIF Number</p>
                    <p class="text-sm text-gray-700">{{ $employee->nhif_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">NSSF Number</p>
                    <p class="text-sm text-gray-700">{{ $employee->nssf_number ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Employment Info --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Employment Details</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Department</p>
                    <p class="text-sm text-gray-700">{{ $employee->department ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Job Title</p>
                    <p class="text-sm text-gray-700">{{ $employee->job_title ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Employment Type</p>
                    <p class="text-sm text-gray-700 capitalize">{{ $employee->employment_type }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Status</p>
                    @php
                        $statusColors = [
                            'active'     => 'bg-emerald-50 text-emerald-600',
                            'probation'  => 'bg-blue-50 text-blue-600',
                            'suspended'  => 'bg-amber-50 text-amber-600',
                            'terminated' => 'bg-red-50 text-red-500',
                            'resigned'   => 'bg-gray-50 text-gray-500',
                        ];
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$employee->employment_status] ?? '' }} capitalize">
                        {{ $employee->employment_status }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Hire Date</p>
                    <p class="text-sm text-gray-700">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Basic Salary</p>
                    <p class="text-sm font-medium text-emerald-900">{{ $employee->basic_salary ? 'KES ' . number_format($employee->basic_salary) : '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Leave History --}}
        <div class="bg-white rounded-xl border border-green-100">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <h2 class="text-sm font-medium text-emerald-900">Leave History</h2>
                <a href="{{ route('tenant.leave-requests.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
            </div>
            @if($leaveRequests->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">No leave requests yet.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Type</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">From</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">To</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Days</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
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
                                    $leaveStatusColors = [
                                        'pending'  => 'bg-amber-50 text-amber-600',
                                        'approved' => 'bg-emerald-50 text-emerald-600',
                                        'rejected' => 'bg-red-50 text-red-500',
                                    ];
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $leaveStatusColors[$leave->status] ?? '' }} capitalize">
                                    {{ $leave->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Payroll History --}}
        <div class="bg-white rounded-xl border border-green-100">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <h2 class="text-sm font-medium text-emerald-900">Payroll History</h2>
                <a href="{{ route('tenant.payroll.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
            </div>
            @if($payrollRecords->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">No payroll records yet.</p>
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
                        @foreach($payrollRecords as $record)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $record->payrollPeriod->name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">KES {{ number_format($record->gross_salary) }}</td>
                            <td class="px-6 py-3 text-sm text-red-500">KES {{ number_format($record->total_deductions) }}</td>
                            <td class="px-6 py-3 text-sm font-medium text-emerald-900">KES {{ number_format($record->net_salary) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Training History --}}
        <div class="bg-white rounded-xl border border-green-100">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <h2 class="text-sm font-medium text-emerald-900">Training & CPD</h2>
                <a href="{{ route('tenant.training.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
            </div>
            @if($trainingEnrollments->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">No training records yet.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Program</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">CPD Points</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Score</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($trainingEnrollments as $enrollment)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $enrollment->trainingProgram->title }}</td>
                            <td class="px-6 py-3 text-sm text-emerald-700">{{ $enrollment->cpd_points_earned }} pts</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $enrollment->score ? $enrollment->score . '%' : '—' }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $enrollmentColors = [
                                        'enrolled'  => 'bg-blue-50 text-blue-600',
                                        'attended'  => 'bg-amber-50 text-amber-600',
                                        'completed' => 'bg-emerald-50 text-emerald-600',
                                        'cancelled' => 'bg-red-50 text-red-500',
                                    ];
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $enrollmentColors[$enrollment->status] ?? '' }} capitalize">
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

        {{-- Emergency Contact --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Emergency Contact</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Name</p>
                    <p class="text-sm text-gray-700">{{ $employee->emergency_contact_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Phone</p>
                    <p class="text-sm text-gray-700">{{ $employee->emergency_contact_phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Relationship</p>
                    <p class="text-sm text-gray-700">{{ $employee->emergency_contact_relationship ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Professional Licenses --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-emerald-900">Licenses</h2>
                <a href="{{ route('tenant.licenses.create') }}" class="text-xs text-emerald-600 hover:text-emerald-800">+ Add</a>
            </div>
            @if($licenses->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">No licenses tracked.</p>
            @else
                <div class="space-y-3">
                    @foreach($licenses as $license)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-emerald-900">{{ $license->license_name }}</p>
                        <p class="text-xs text-gray-400">{{ $license->license_number }}</p>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-xs text-gray-400">Expires: {{ $license->expiry_date->format('M d, Y') }}</p>
                            @php
                                $licColors = [
                                    'valid'    => 'bg-emerald-50 text-emerald-600',
                                    'expiring' => 'bg-amber-50 text-amber-600',
                                    'expired'  => 'bg-red-50 text-red-500',
                                ];
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $licColors[$license->status] ?? '' }} capitalize">
                                {{ $license->status }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Performance Reviews --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-emerald-900">Performance</h2>
                <a href="{{ route('tenant.performance.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
            </div>
            @if($performanceReviews->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">No reviews yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($performanceReviews as $review)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-emerald-900">{{ $review->review_period }} {{ $review->review_year }}</p>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $review->review_type) }}</p>
                            @if($review->final_rating)
                                <span class="text-xs font-medium text-emerald-700">{{ $review->final_rating }}/5</span>
                            @else
                                <span class="text-xs text-gray-400">Not rated</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Disciplinary Cases --}}
        @if($disciplinaryCases->isNotEmpty())
        <div class="bg-white rounded-xl border border-red-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-red-700">Disciplinary Cases</h2>
                <a href="{{ route('tenant.disciplinary.index') }}" class="text-xs text-red-500 hover:text-red-700">View all →</a>
            </div>
            <div class="space-y-3">
                @foreach($disciplinaryCases as $case)
                <div class="p-3 bg-red-50 rounded-lg">
                    <p class="text-sm font-medium text-red-800">{{ $case->title }}</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-xs text-red-400 capitalize">{{ str_replace('_', ' ', $case->type) }}</p>
                        <span class="text-xs text-red-500 capitalize">{{ $case->status }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('tenant.employees.edit', $employee) }}"
                   class="block w-full text-center text-sm bg-emerald-50 text-emerald-700 hover:bg-emerald-100 py-2 rounded-lg transition-colors">
                    Edit Profile
                </a>
                <a href="{{ route('tenant.onboarding.show', $employee) }}"
                   class="block w-full text-center text-sm bg-purple-50 text-purple-700 hover:bg-purple-100 py-2 rounded-lg transition-colors">
                    Onboarding Checklist
                </a>
                <a href="{{ route('tenant.leave-requests.create') }}"
                   class="block w-full text-center text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 py-2 rounded-lg transition-colors">
                    Apply Leave
                </a>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-2">Transfer Branch</p>
                    <form method="POST" action="{{ route('tenant.employees.transfer', $employee) }}">
                        @csrf
                        <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-2">
                            <option value="">Head Office (No Branch)</option>
                            @foreach(\App\Models\Branch::where('tenant_id', tenant('id'))->get() as $br)
                                <option value="{{ $br->id }}" {{ $employee->branch_id == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full text-sm bg-amber-50 text-amber-700 hover:bg-amber-100 py-2 rounded-lg transition-colors">Transfer</button>
                    </form>
                </div>
                <a href="{{ route('tenant.disciplinary.create') }}"
                   class="block w-full text-center text-sm bg-amber-50 text-amber-700 hover:bg-amber-100 py-2 rounded-lg transition-colors">
                    File Disciplinary Case
                </a>
                <form method="POST" action="{{ route('tenant.employees.destroy', $employee) }}"
                      onsubmit="return confirm('Are you sure you want to delete this employee?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full text-sm bg-red-50 text-red-500 hover:bg-red-100 py-2 rounded-lg transition-colors">
                        Delete Employee
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

@endsection

