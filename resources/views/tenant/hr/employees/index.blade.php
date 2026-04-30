@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Employees')
@section('page-subtitle', 'Manage all staff members')

@section('page-actions')
    <a href="{{ route('tenant.exports.employees') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50">Export Excel</a>

    <a href="{{ route('tenant.employees.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">+ Add Employee</a>
    </a>
@endsection
@section('content')
    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-green-100 p-4 mb-4">
        <form method="GET" class="flex gap-3 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees..." class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            @if(isset($branches) && $branches->count() > 0)
            <select name="branch_id" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"><option value="">All Branches</option><option value="none" {{ request('branch_id') === 'none' ? 'selected' : '' }}>No Branch</option>@foreach($branches as $br)<option value="{{ $br->id }}" {{ request('branch_id') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>@endforeach</select>
            @endif
            <select name="status" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"><option value="">All Status</option><option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option><option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option></select>
            <button type="submit" class="bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
            @if(request()->hasAny(['search', 'branch_id', 'status']))<a href="{{ route('tenant.employees.index') }}" class="bg-gray-100 text-gray-600 text-sm px-4 py-2 rounded-lg">Clear</a>@endif
        </form>
    </div>

    {{-- Subscription Usage --}}
    @if(isset($limit) && $limit['max'] < PHP_INT_MAX)
    @php $pct = $limit['max'] > 0 ? min(100, ($limit['current'] / $limit['max']) * 100) : 0; @endphp
    <div class="mb-4 bg-white rounded-xl border border-green-100 px-5 py-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-gray-600">Employee Usage</span>
                <span class="text-xs text-gray-400">({{ $limit['plan_name'] }} Plan)</span>
            </div>
            <span class="text-xs font-semibold {{ $limit['at_limit'] ? 'text-red-600' : 'text-emerald-700' }}">
                {{ $limit['current'] }} / {{ $limit['max'] }}
            </span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="h-2 rounded-full {{ $pct >= 100 ? 'bg-red-500' : ($pct >= 80 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                 style="width: {{ $pct }}%"></div>
        </div>
        @if($limit['at_limit'])
            <p class="text-xs text-red-600 mt-2">Employee limit reached. Contact support or upgrade your plan to add more employees.</p>
        @elseif($limit['remaining'] <= 5)
            <p class="text-xs text-amber-600 mt-2">Only {{ $limit['remaining'] }} employee slot{{ $limit['remaining'] === 1 ? '' : 's' }} remaining on your current plan.</p>
        @endif
    </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($employees->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No employees added yet.</p>
                <a href="{{ route('tenant.employees.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Add your first employee →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Department</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Job Title</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">License</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($employees as $employee)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-medium flex-shrink-0">
                                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-emerald-900">{{ $employee->full_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $employee->employee_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $employee->department ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $employee->job_title ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-500 capitalize">{{ $employee->employment_type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'active'     => 'bg-emerald-50 text-emerald-600',
                                    'probation'  => 'bg-blue-50 text-blue-600',
                                    'suspended'  => 'bg-amber-50 text-amber-600',
                                    'terminated' => 'bg-red-50 text-red-500',
                                    'resigned'   => 'bg-gray-50 text-gray-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$employee->employment_status] ?? 'bg-gray-50 text-gray-500' }} capitalize">
                                {{ $employee->employment_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($employee->license_status === 'not_applicable')
                                <span class="text-xs text-gray-400">N/A</span>
                            @else
                                @php
                                    $licenseColors = [
                                        'valid'    => 'bg-emerald-50 text-emerald-600',
                                        'expiring' => 'bg-amber-50 text-amber-600',
                                        'expired'  => 'bg-red-50 text-red-500',
                                    ];
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $licenseColors[$employee->license_status] ?? '' }} capitalize">
                                    {{ $employee->license_status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.employees.show', $employee) }}"
                                   class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                <a href="{{ route('tenant.employees.edit', $employee) }}"
                                   class="text-xs text-blue-500 hover:text-blue-700">Edit</a>
                                <form method="POST" action="{{ route('tenant.employees.destroy', $employee) }}"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($employees->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $employees->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection

