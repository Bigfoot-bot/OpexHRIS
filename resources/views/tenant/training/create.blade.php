@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'New Training Program')
@section('page-subtitle', 'Create a new training or CPD program')

@section('page-actions')
    <a href="{{ route('tenant.training.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Training
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

    <form method="POST" action="{{ route('tenant.training.store') }}">
        @csrf

        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Program Details</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="e.g. BLS/ACLS Certification"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                              placeholder="Program description...">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Type *</label>
                        <select name="type" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="internal" {{ old('type') === 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="external" {{ old('type') === 'external' ? 'selected' : '' }}>External</option>
                            <option value="online" {{ old('type') === 'online' ? 'selected' : '' }}>Online</option>
                            <option value="conference" {{ old('type') === 'conference' ? 'selected' : '' }}>Conference</option>
                            <option value="workshop" {{ old('type') === 'workshop' ? 'selected' : '' }}>Workshop</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Category *</label>
                        <select name="category" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="clinical" {{ old('category') === 'clinical' ? 'selected' : '' }}>Clinical</option>
                            <option value="administrative" {{ old('category') === 'administrative' ? 'selected' : '' }}>Administrative</option>
                            <option value="compliance" {{ old('category') === 'compliance' ? 'selected' : '' }}>Compliance</option>
                            <option value="leadership" {{ old('category') === 'leadership' ? 'selected' : '' }}>Leadership</option>
                            <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                            <option value="soft_skills" {{ old('category') === 'soft_skills' ? 'selected' : '' }}>Soft Skills</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Provider</label>
                        <input type="text" name="provider" value="{{ old('provider') }}"
                               placeholder="e.g. Kenya Red Cross"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Location</label>
                        <input type="text" name="location" value="{{ old('location') }}"
                               placeholder="e.g. Nairobi / Online"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">CPD Points</label>
                        <input type="number" name="cpd_points" value="{{ old('cpd_points', 0) }}" min="0"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Cost (KES)</label>
                        <input type="number" name="cost" value="{{ old('cost', 0) }}" min="0"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Max Participants</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants') }}" min="1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
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

            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                Create Program
            </button>
            <a href="{{ route('tenant.training.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
        </div>

    </form>
</div>

@endsection
