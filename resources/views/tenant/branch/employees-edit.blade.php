@extends('tenant.branch.layout')
@section('page-title', 'Edit Employee')
@section('page-subtitle', 'Update employee information')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('tenant.branch.employees.update', [$branch, $employee]) }}" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">First Name *</label>
                    <input type="text" name="first_name" required value="{{ old('first_name', $employee->first_name) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Last Name *</label>
                    <input type="text" name="last_name" required value="{{ old('last_name', $employee->last_name) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Email *</label>
                    <input type="email" name="email" required value="{{ old('email', $employee->email) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Job Title</label>
                    <input type="text" name="job_title" value="{{ old('job_title', $employee->job_title) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Department</label>
                    <input type="text" name="department" value="{{ old('department', $employee->department) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employment Status</label>
                    <select name="employment_status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="active" {{ $employee->employment_status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $employee->employment_status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ $employee->employment_status === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employment Type</label>
                    <select name="employment_type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="permanent" {{ $employee->employment_type === 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="contract" {{ $employee->employment_type === 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="casual" {{ $employee->employment_type === 'casual' ? 'selected' : '' }}>Casual</option>
                        <option value="internship" {{ $employee->employment_type === 'internship' ? 'selected' : '' }}>Internship</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2 rounded-lg">Update Employee</button>
                <a href="{{ route('tenant.branch.employees', $branch) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
