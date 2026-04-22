@extends('tenant.layouts.app')
@section('page-title', $branch->name . ' Portal')
@section('page-subtitle', 'Branch HR Portal')
@section('page-actions')
    <a href="{{ route('tenant.branches.show', $branch) }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">
        Back to Branch
    </a>
@endsection
@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $employeeCount }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Employees</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $employees->where('employment_status', 'active')->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Active</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">KES {{ $budget ? number_format($budget->allocated_amount, 0) : '0' }}</p>
            <p class="text-xs text-gray-400 mt-1">Budget Allocated</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">KES {{ $budget ? number_format($budget->remaining, 0) : '0' }}</p>
            <p class="text-xs text-gray-400 mt-1">Budget Remaining</p>
        </div>
    </div>

    {{-- Employees --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-semibold text-gray-800">Branch Employees</h2>
        </div>
        @if($employees->isEmpty())
            <div class="p-12 text-center">
                <p class="text-gray-400 text-sm">No employees in this branch.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Department</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Job Title</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($employees as $emp)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                    <span class="text-xs font-medium text-emerald-700">{{ substr($emp->first_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $emp->first_name }} {{ $emp->last_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $emp->employee_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $emp->department ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $emp->job_title ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $emp->employment_status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }} capitalize">
                                {{ $emp->employment_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tenant.employees.show', $emp) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
