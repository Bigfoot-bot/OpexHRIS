@extends('central.layouts.app')

@section('title', 'New Announcement')
@section('page-title', 'New Announcement')
@section('page-subtitle', 'Send an announcement to facilities')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">

        @if($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @csrf
            <div class="space-y-5">

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Announcement title"/>
                </div>

                {{-- Message --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Message *</label>
                    <textarea name="body" rows="6" required
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                              placeholder="Write your announcement here...">{{ old('body') }}</textarea>
                </div>

                {{-- Meeting Link --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Meeting Link <span class="text-gray-400">(optional)</span></label>
                    <input type="url" name="meeting_link" value="{{ old('meeting_link') }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="https://meet.google.com/xxx or https://zoom.us/j/xxx"/>
                </div>

                {{-- Audience --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-2">Send To *</label>
                    <div class="flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="audience" value="all" class="sr-only peer"
                                {{ old('audience', 'all') === 'all' ? 'checked' : '' }}>
                            <div class="border-2 rounded-xl p-3 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 border-gray-200 hover:border-gray-300">
                                <svg class="w-5 h-5 mx-auto mb-1 text-gray-400 peer-checked:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                </svg>
                                <p class="text-xs font-semibold text-gray-700">All Facilities</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $tenants->count() }} active</p>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="audience" value="specific" class="sr-only peer"
                                {{ old('audience') === 'specific' ? 'checked' : '' }}>
                            <div class="border-2 rounded-xl p-3 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 border-gray-200 hover:border-gray-300">
                                <svg class="w-5 h-5 mx-auto mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <p class="text-xs font-semibold text-gray-700">Specific Facilities</p>
                                <p class="text-xs text-gray-400 mt-0.5">Choose one or more</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Facility Picker (shown only when specific is selected) --}}
                <div id="facility-picker" class="{{ old('audience') === 'specific' ? '' : 'hidden' }} space-y-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Select Facilities *</label>

                    {{-- Search --}}
                    <input type="text" id="facility-search" placeholder="Search facilities..."
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-2"/>

                    {{-- Select All / None --}}
                    <div class="flex gap-3 mb-2">
                        <button type="button" onclick="selectAllFacilities()"
                                class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Select All</button>
                        <button type="button" onclick="deselectAllFacilities()"
                                class="text-xs text-gray-400 hover:text-gray-600">Clear</button>
                        <span id="selected-count" class="text-xs text-gray-400 ml-auto">0 selected</span>
                    </div>

                    {{-- Facility List --}}
                    <div class="border border-gray-200 rounded-xl divide-y divide-gray-100 max-h-60 overflow-y-auto" id="facility-list">
                        @foreach($tenants as $tenant)
                        <label class="facility-item flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer"
                               data-name="{{ strtolower($tenant->name) }}">
                            <input type="checkbox" name="tenant_ids[]" value="{{ $tenant->id }}"
                                   class="facility-checkbox w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"
                                   {{ in_array($tenant->id, old('tenant_ids', [])) ? 'checked' : '' }}>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700 truncate">{{ $tenant->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $tenant->email }}</p>
                            </div>
                            @if($tenant->is_active)
                                <span class="text-xs bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full flex-shrink-0">Active</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Email toggle --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="send_email" id="send_email" value="1" checked
                           class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"/>
                    <label for="send_email" class="text-sm text-gray-600">Send email notification to facility admin(s)</label>
                </div>

            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Publish Announcement
                </button>
                <a href="{{ route('admin.announcements.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Audience toggle
    document.querySelectorAll('input[name="audience"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.getElementById('facility-picker').classList.toggle('hidden', radio.value !== 'specific');
            if (radio.value !== 'specific') deselectAllFacilities();
        });
    });

    // Search
    document.getElementById('facility-search').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.facility-item').forEach(item => {
            item.style.display = item.dataset.name.includes(q) ? '' : 'none';
        });
    });

    // Select / deselect all
    function selectAllFacilities() {
        document.querySelectorAll('.facility-checkbox').forEach(cb => cb.checked = true);
        updateCount();
    }
    function deselectAllFacilities() {
        document.querySelectorAll('.facility-checkbox').forEach(cb => cb.checked = false);
        updateCount();
    }

    // Count
    function updateCount() {
        const n = document.querySelectorAll('.facility-checkbox:checked').length;
        document.getElementById('selected-count').textContent = n + ' selected';
    }
    document.querySelectorAll('.facility-checkbox').forEach(cb => {
        cb.addEventListener('change', updateCount);
    });
    updateCount();
</script>
@endsection
