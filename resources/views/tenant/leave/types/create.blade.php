@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Add Leave Type')
@section('page-subtitle', 'Configure a new leave type')

@section('page-actions')
    <a href="{{ route('tenant.leave-types.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Leave Types
    </a>
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

    <form method="POST" action="{{ route('tenant.leave-types.store') }}">
        @csrf

        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Leave Type Details</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g. Annual Leave"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Code</label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               placeholder="e.g. AL"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Days Allowed Per Year *</label>
                    <input type="number" name="days_allowed" value="{{ old('days_allowed', 0) }}" required min="0"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                {{-- Toggles --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-700">Paid Leave</p>
                            <p class="text-xs text-gray-400">Employee is paid during this leave</p>
                        </div>
                        <input type="checkbox" name="is_paid" value="1" {{ old('is_paid', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600"/>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-700">Requires Document</p>
                            <p class="text-xs text-gray-400">Medical certificate or proof required</p>
                        </div>
                        <input type="checkbox" name="requires_document" value="1" {{ old('requires_document') ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600"/>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-700">Allow Half Day</p>
                            <p class="text-xs text-gray-400">Employee can apply for half day</p>
                        </div>
                        <input type="checkbox" name="allow_half_day" value="1" {{ old('allow_half_day') ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600"/>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-700">Carry Forward</p>
                            <p class="text-xs text-gray-400">Unused days carry to next year</p>
                        </div>
                        <input type="checkbox" name="carry_forward" value="1" {{ old('carry_forward') ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600"/>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Max Carry Forward Days</label>
                    <input type="number" name="max_carry_forward_days" value="{{ old('max_carry_forward_days', 0) }}" min="0"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                Save Leave Type
            </button>
            <a href="{{ route('tenant.leave-types.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
        </div>

    </form>
</div>

@endsection
