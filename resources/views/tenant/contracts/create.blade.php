@extends((auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0) && !auth()->user()->isInEmployeePortal() ? 'tenant.layouts.app' : 'tenant.employee.layouts.app')
@section('page-title', 'New Contract')
@section('page-subtitle', 'Create a new employee contract')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('tenant.contracts.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee *</label>
                    <select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Contract Title *</label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Employment Agreement"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Contract Type *</label>
                    <select name="contract_type" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="permanent" {{ old('contract_type') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="fixed_term" {{ old('contract_type') == 'fixed_term' ? 'selected' : '' }}>Fixed Term</option>
                        <option value="casual" {{ old('contract_type') == 'casual' ? 'selected' : '' }}>Casual</option>
                        <option value="internship" {{ old('contract_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                        <option value="consultant" {{ old('contract_type') == 'consultant' ? 'selected' : '' }}>Consultant</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Job Title</label>
                    <input type="text" name="job_title" value="{{ old('job_title') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Start Date *</label>
                    <input type="date" name="start_date" required value="{{ old('start_date') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">End Date <span class="text-gray-400">(leave blank for permanent)</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Salary (KES)</label>
                    <input type="number" name="salary" value="{{ old('salary') }}" min="0"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Department</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Upload Contract File (PDF/DOCX)</label>
                <input type="file" name="contract_file" accept=".pdf,.doc,.docx" class="w-full text-sm"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2 rounded-lg">Create Contract</button>
                <a href="{{ route('tenant.contracts.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection


