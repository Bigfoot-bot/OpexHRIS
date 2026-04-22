@extends('tenant.branch.layout')
@section('page-title', 'Add Employee')
@section('page-subtitle', 'Add a new employee to this branch')
@section('page-actions')
    <a href="{{ route('tenant.branch.employees', $branch) }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">
        Back
    </a>
@endsection
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        @if(session('error'))
            <div class="bg-red-50 border border-red-100 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('tenant.branch.employees.store', $branch) }}" class="space-y-4">
            @csrf
            @if($errors->any())
                <div class="bg-red-50 border border-red-100 text-red-700 text-sm rounded-lg px-4 py-3">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">First Name *</label>
                    <input type="text" name="first_name" required value="{{ old('first_name') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Last Name *</label>
                    <input type="text" name="last_name" required value="{{ old('last_name') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Email *</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Job Title</label>
                    <input type="text" name="job_title" value="{{ old('job_title') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Department</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Hire Date</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employment Type</label>
                    <select name="employment_type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="permanent">Permanent</option>
                        <option value="contract">Contract</option>
                        <option value="casual">Casual</option>
                        <option value="internship">Internship</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2 rounded-lg">Add Employee</button>
                <a href="{{ route('tenant.branch.employees', $branch) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

