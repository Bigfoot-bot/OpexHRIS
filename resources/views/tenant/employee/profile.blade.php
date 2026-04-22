@extends('tenant.employee.layouts.app')

@section('page-title', 'My Profile')
@section('page-subtitle', $employee->employee_number . ' — ' . $employee->department)

@section('content')

<div class="grid grid-cols-3 gap-5">

    {{-- Left Column --}}
    <div class="col-span-2 space-y-5">

        {{-- Personal Info --}}
        <div class="bg-white rounded-xl border border-blue-100 p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xl font-medium">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-base font-medium text-blue-900">{{ $employee->full_name }}</h2>
                    <p class="text-sm text-gray-400">{{ $employee->employee_number }} · {{ $employee->job_title }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
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
        <div class="bg-white rounded-xl border border-blue-100 p-6">
            <h2 class="text-sm font-medium text-blue-900 mb-5">Employment Details</h2>
            <div class="grid grid-cols-2 gap-4">
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
                    <p class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $employee->employment_type) }}</p>
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
                    <p class="text-xs text-gray-400 mb-1">Specialty</p>
                    <p class="text-sm text-gray-700">{{ $employee->specialty ?? '—' }}</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Emergency Contact --}}
        <div class="bg-white rounded-xl border border-blue-100 p-6">
            <h2 class="text-sm font-medium text-blue-900 mb-4">Emergency Contact</h2>
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

        {{-- Professional Info --}}
        <div class="bg-white rounded-xl border border-blue-100 p-6">
            <h2 class="text-sm font-medium text-blue-900 mb-4">Professional Info</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Professional Cadre</p>
                    <p class="text-sm text-gray-700">{{ $employee->professional_cadre ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Registration Body</p>
                    <p class="text-sm text-gray-700">{{ $employee->registration_body ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Registration Number</p>
                    <p class="text-sm text-gray-700">{{ $employee->registration_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">License Expiry</p>
                    <p class="text-sm text-gray-700">{{ $employee->license_expiry_date ? $employee->license_expiry_date->format('M d, Y') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">CPD Points Required</p>
                    <p class="text-sm text-gray-700">{{ $employee->cpd_points_required ?? '—' }}</p>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection