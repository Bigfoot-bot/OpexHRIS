@extends(auth()->user()->isInEmployeePortal() ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Apply for Leave')
@section('page-subtitle', 'Submit a new leave request')

@section('page-actions')
    @if($fromPortal)
        <a href="{{ route('tenant.employee.leave') }}"
           class="text-sm text-gray-500 hover:text-blue-700">
            ← Back to My Leave
        </a>
    @else
        <a href="{{ route('tenant.leave-requests.index') }}"
           class="text-sm text-gray-500 hover:text-emerald-700">
            ← Back to Leave Requests
        </a>
    @endif
@endsection

@section('content')

<div class="max-w-2xl">

    @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tenant.leave-requests.store') }}">
        @csrf
        <input type="hidden" name="from_portal" value="{{ $fromPortal ? '1' : '0' }}"/>

        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Leave Request Details</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee *</label>
                    @if($fromPortal && $selectedEmployee)
                        <input type="hidden" name="employee_id" value="{{ $selectedEmployee->id }}"/>
                        <div class="w-full px-3 py-2 rounded-lg border border-gray-100 bg-gray-50 text-sm text-gray-600">
                            {{ $selectedEmployee->full_name }} — {{ $selectedEmployee->job_title }}
                        </div>
                    @else
                        <select name="employee_id" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} — {{ $employee->job_title }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Leave Type *</label>
                    <select name="leave_type_id" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->days_allowed }} days allowed)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Start Date *</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">End Date *</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_half_day" id="is_half_day" value="1"
                           {{ old('is_half_day') ? 'checked' : '' }}
                           class="w-4 h-4 text-emerald-600 rounded"/>
                    <label for="is_half_day" class="text-sm text-gray-600">Half day</label>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Reason</label>
                    <textarea name="reason" rows="3"
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                              placeholder="Optional reason for leave...">{{ old('reason') }}</textarea>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                Submit Leave Request
            </button>
            @if($fromPortal)
                <a href="{{ route('tenant.employee.leave') }}"
                   class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
            @else
                <a href="{{ route('tenant.leave-requests.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
            @endif
        </div>

    </form>
</div>

@endsection
