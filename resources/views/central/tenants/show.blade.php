@extends('central.layouts.app')

@section('page-title', $tenant->name)
@section('page-subtitle', $tenant->domains->first()?->domain ?? $tenant->slug . '.' . parse_url(config('app.url'), PHP_URL_HOST))

@section('page-actions')
    <a href="{{ route('admin.tenants.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Facilities
    </a>
@endsection

@section('content')

@php
    $employees   = \App\Models\Tenant\Employee::withoutGlobalScopes()->where('tenant_id', $tenant->id)->get();
    $users       = \App\Models\Tenant\User::withoutGlobalScopes()->where('tenant_id', $tenant->id)->get();
    $payrollTotal = \App\Models\Tenant\PayrollRecord::withoutGlobalScopes()
                    ->whereHas('payrollPeriod', fn($q) => $q->where('tenant_id', $tenant->id))
                    ->sum('net_salary');
    $leaveCount  = \App\Models\Tenant\LeaveRequest::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count();
    $licenseCount = \App\Models\Tenant\ProfessionalLicense::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count();
@endphp

<div class="grid grid-cols-3 gap-5">

    {{-- Left Column --}}
    <div class="col-span-2 space-y-5">

        {{-- Usage Stats --}}
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-green-100 p-4">
                <p class="text-xs text-gray-400 mb-1">Employees</p>
                <p class="text-2xl font-medium text-emerald-900">{{ $employees->count() }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $employees->where('employment_status', 'active')->count() }} active</p>
            </div>
            <div class="bg-white rounded-xl border border-green-100 p-4">
                <p class="text-xs text-gray-400 mb-1">Portal Users</p>
                <p class="text-2xl font-medium text-emerald-900">{{ $users->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-green-100 p-4">
                <p class="text-xs text-gray-400 mb-1">Leave Requests</p>
                <p class="text-2xl font-medium text-emerald-900">{{ $leaveCount }}</p>
            </div>
            <div class="bg-white rounded-xl border border-green-100 p-4">
                <p class="text-xs text-gray-400 mb-1">Licenses</p>
                <p class="text-2xl font-medium text-emerald-900">{{ $licenseCount }}</p>
            </div>
        </div>

        {{-- Facility Info --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Facility Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Facility Name</p>
                    <p class="text-sm text-gray-700">{{ $tenant->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Email</p>
                    <p class="text-sm text-gray-700">{{ $tenant->email ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Phone</p>
                    <p class="text-sm text-gray-700">{{ $tenant->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Domain</p>
                    @php $tenantDomain = $tenant->domains->first()?->domain ?? $tenant->slug . '.' . parse_url(config('app.url'), PHP_URL_HOST); @endphp
                    <a href="https://{{ $tenantDomain }}/login"
                       target="_blank"
                       class="text-sm text-emerald-600 hover:text-emerald-800">
                        {{ $tenantDomain }} ↗
                    </a>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Joined</p>
                    <p class="text-sm text-gray-700">{{ $tenant->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Trial Ends</p>
                    <p class="text-sm text-gray-700">{{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('M d, Y') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Subscription Ends</p>
                    <p class="text-sm text-gray-700">{{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('M d, Y') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Total Payroll Processed</p>
                    <p class="text-sm font-medium text-emerald-900">KES {{ number_format($payrollTotal) }}</p>
                </div>
            </div>
        </div>

        {{-- Employees --}}
        <div class="bg-white rounded-xl border border-green-100">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="text-sm font-medium text-emerald-900">Employees</h2>
            </div>
            @if($employees->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">No employees yet.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Name</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Department</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Hired</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($employees->take(10) as $employee)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <p class="text-sm font-medium text-emerald-900">{{ $employee->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $employee->job_title }}</p>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $employee->department ?? '—' }}</td>
                            <td class="px-6 py-3">
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
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-400">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Portal Users --}}
        <div class="bg-white rounded-xl border border-green-100">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="text-sm font-medium text-emerald-900">Portal Users</h2>
            </div>
            @if($users->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">No users found.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Name</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Email</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $user->name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-3">
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-400">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Status & Plan --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Subscription</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Status</span>
                    @if($tenant->is_active)
                        <span class="text-xs bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full">Active</span>
                    @else
                        <span class="text-xs bg-red-50 text-red-500 px-2.5 py-1 rounded-full">Suspended</span>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Plan</span>
                    @php
                        $planColors = [
                            'basic'        => 'bg-gray-50 text-gray-500',
                            'professional' => 'bg-blue-50 text-blue-600',
                            'enterprise'   => 'bg-emerald-50 text-emerald-600',
                        ];
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $planColors[$tenant->subscription_plan] ?? '' }} capitalize">
                        {{ $tenant->subscription_plan }}
                    </span>
                </div>
                @php $currentPlan = $plans->firstWhere('name', $tenant->subscription_plan); @endphp
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Monthly Fee</span>
                    <span class="text-sm font-medium text-emerald-900">
                        KES {{ $currentPlan ? number_format($currentPlan->monthly_price) : '—' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Employee Limit</span>
                    <span class="text-sm text-gray-700">
                        {{ $currentPlan?->max_employees ?? '—' }}
                    </span>
                </div>
            </div>

            {{-- Update Plan --}}
            <div class="mt-4 pt-4 border-t border-gray-50">
                <form method="POST" action="{{ route('admin.tenants.update-plan', $tenant) }}">
                    @csrf
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Change Plan</label>
                    <select name="subscription_plan"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-2">
                        @foreach($plans as $plan)
                        <option value="{{ $plan->name }}" {{ $tenant->subscription_plan === $plan->name ? 'selected' : '' }}>
                            {{ ucfirst($plan->name) }} — KES {{ number_format($plan->monthly_price) }}/mo
                        </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                        Update Plan
                    </button>
                </form>
            </div>

            {{-- Actions --}}
            <div class="mt-4 pt-4 border-t border-gray-50 space-y-2">
                <form method="POST" action="{{ route('admin.tenants.toggle-status', $tenant) }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-sm {{ $tenant->is_active ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}
                                   py-2 rounded-lg transition-colors duration-150">
                        {{ $tenant->is_active ? 'Suspend Facility' : 'Activate Facility' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}"
                      onsubmit="return confirm('Are you sure? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full text-sm bg-red-50 text-red-500 hover:bg-red-100 py-2 rounded-lg transition-colors duration-150">
                        Delete Facility
                    </button>
                </form>
            </div>
        </div>

        {{-- Facility ID --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Technical Info</h2>
            <div class="space-y-2">
                <div>
                    <p class="text-xs text-gray-400">Tenant ID</p>
                    <p class="text-xs font-mono text-gray-600 break-all">{{ $tenant->id }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Slug</p>
                    <p class="text-xs font-mono text-gray-600">{{ $tenant->slug }}</p>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection