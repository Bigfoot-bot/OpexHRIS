@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Edit Position')
@section('page-subtitle', $position->title)

@section('page-actions')
    <a href="{{ route('tenant.positions.show', $position) }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Position
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

    <form method="POST" action="{{ route('tenant.positions.update', $position) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Position Details</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Job Title *</label>
                    <input type="text" name="title" value="{{ old('title', $position->title) }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Department *</label>
                        <input type="text" name="department" value="{{ old('department', $position->department) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Location</label>
                        <input type="text" name="location" value="{{ old('location', $position->location) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Type *</label>
                        <select name="type" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="full_time" {{ old('type', $position->type) === 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ old('type', $position->type) === 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ old('type', $position->type) === 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="intern" {{ old('type', $position->type) === 'intern' ? 'selected' : '' }}>Internship</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Status *</label>
                        <select name="status" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="draft" {{ old('status', $position->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="open" {{ old('status', $position->status) === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="on_hold" {{ old('status', $position->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="closed" {{ old('status', $position->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Vacancies *</label>
                        <input type="number" name="vacancies" value="{{ old('vacancies', $position->vacancies) }}" required min="1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Min Salary (KES)</label>
                        <input type="number" name="salary_min" value="{{ old('salary_min', $position->salary_min) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Max Salary (KES)</label>
                        <input type="number" name="salary_max" value="{{ old('salary_max', $position->salary_max) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Closing Date</label>
                        <input type="date" name="closing_date" value="{{ old('closing_date', $position->closing_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Job Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description', $position->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Requirements</label>
                    <textarea name="requirements" rows="3"
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('requirements', $position->requirements) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Responsibilities</label>
                    <textarea name="responsibilities" rows="3"
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('responsibilities', $position->responsibilities) }}</textarea>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                Update Position
            </button>
            <a href="{{ route('tenant.positions.show', $position) }}"
               class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
        </div>

    </form>
</div>

@endsection
