@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'New Disciplinary Case')
@section('page-subtitle', 'File a new disciplinary case')

@section('page-actions')
    <a href="{{ route('tenant.disciplinary.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Cases
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

    <form method="POST" action="{{ route('tenant.disciplinary.store') }}">
        @csrf

        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Case Details</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee *</label>
                    <select name="employee_id" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} — {{ $employee->job_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Case Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="Brief title of the case"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Description *</label>
                    <textarea name="description" rows="4" required
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                              placeholder="Detailed description of the incident...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Type *</label>
                        <select name="type" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="verbal_warning" {{ old('type') === 'verbal_warning' ? 'selected' : '' }}>Verbal Warning</option>
                            <option value="written_warning" {{ old('type') === 'written_warning' ? 'selected' : '' }}>Written Warning</option>
                            <option value="final_warning" {{ old('type') === 'final_warning' ? 'selected' : '' }}>Final Warning</option>
                            <option value="suspension" {{ old('type') === 'suspension' ? 'selected' : '' }}>Suspension</option>
                            <option value="termination" {{ old('type') === 'termination' ? 'selected' : '' }}>Termination</option>
                            <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Severity *</label>
                        <select name="severity" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="minor" {{ old('severity') === 'minor' ? 'selected' : '' }}>Minor</option>
                            <option value="moderate" {{ old('severity') === 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="serious" {{ old('severity') === 'serious' ? 'selected' : '' }}>Serious</option>
                            <option value="gross_misconduct" {{ old('severity') === 'gross_misconduct' ? 'selected' : '' }}>Gross Misconduct</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Incident Date *</label>
                        <input type="date" name="incident_date" value="{{ old('incident_date') }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Hearing Date</label>
                        <input type="date" name="hearing_date" value="{{ old('hearing_date') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                File Case
            </button>
            <a href="{{ route('tenant.disciplinary.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
        </div>

    </form>
</div>

@endsection
