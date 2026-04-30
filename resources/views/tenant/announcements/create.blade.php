@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('title', 'New Announcement')
@section('page-title', 'New Announcement')
@section('page-subtitle', 'Send an announcement to employees')

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

        <form method="POST" action="{{ route('tenant.announcements.store') }}">
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
                            <input type="radio" name="audience" value="all_employees" id="aud-all"
                                   {{ old('audience', 'all_employees') === 'all_employees' ? 'checked' : '' }}
                                   style="position:absolute;opacity:0;width:0;height:0;">
                            <div id="card-all"
                                 class="border-2 rounded-xl p-3 text-center transition-all border-emerald-500 bg-emerald-50">
                                <svg class="w-5 h-5 mx-auto mb-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-xs font-semibold text-gray-700">All Employees</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $employees->count() }} active</p>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="audience" value="specific_employees" id="aud-specific"
                                   {{ old('audience') === 'specific_employees' ? 'checked' : '' }}
                                   style="position:absolute;opacity:0;width:0;height:0;">
                            <div id="card-specific"
                                 class="border-2 rounded-xl p-3 text-center transition-all border-gray-200 hover:border-gray-300">
                                <svg class="w-5 h-5 mx-auto mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <p class="text-xs font-semibold text-gray-700">Specific Employees</p>
                                <p class="text-xs text-gray-400 mt-0.5">Choose one or more</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Employee Picker --}}
                <div id="employee-picker" style="{{ old('audience') === 'specific_employees' ? '' : 'display:none;' }}" class="space-y-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Select Employees *</label>

                    {{-- Branch filter --}}
                    @if($branches->count() > 1)
                    <select id="branch-filter" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-2">
                        <option value="">All branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    {{-- Search --}}
                    <input type="text" id="employee-search" placeholder="Search employees..."
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-2"/>

                    {{-- Select All / None --}}
                    <div class="flex gap-3 mb-2">
                        <button type="button" onclick="selectAllEmployees()"
                                class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Select All</button>
                        <button type="button" onclick="deselectAllEmployees()"
                                class="text-xs text-gray-400 hover:text-gray-600">Clear</button>
                        <span id="emp-selected-count" class="text-xs text-gray-400 ml-auto">0 selected</span>
                    </div>

                    {{-- Employee List --}}
                    <div class="border border-gray-200 rounded-xl divide-y divide-gray-100 max-h-64 overflow-y-auto" id="employee-list">
                        @foreach($employees as $emp)
                        <label class="employee-item flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer"
                               data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}"
                               data-branch="{{ $emp->branch_id }}">
                            <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}"
                                   class="employee-checkbox w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"
                                   {{ in_array($emp->id, old('employee_ids', [])) ? 'checked' : '' }}>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700 truncate">{{ $emp->first_name }} {{ $emp->last_name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $emp->job_title ?? $emp->department ?? '' }}{{ $emp->branch ? ' &mdash; ' . $emp->branch->name : '' }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Email toggle --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="send_email" id="send_email" value="1" checked
                           class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"/>
                    <label for="send_email" class="text-sm text-gray-600">Send email notification</label>
                </div>

            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Publish Announcement
                </button>
                <a href="{{ route('tenant.announcements.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Audience toggle
function setAudience(value) {
    var isSpecific = value === 'specific_employees';
    document.getElementById('employee-picker').style.display = isSpecific ? '' : 'none';
    if (!isSpecific) deselectAllEmployees();

    document.getElementById('card-all').className = 'border-2 rounded-xl p-3 text-center transition-all ' +
        (value === 'all_employees' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300');
    document.getElementById('card-specific').className = 'border-2 rounded-xl p-3 text-center transition-all ' +
        (isSpecific ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300');
}

document.getElementById('aud-all').addEventListener('change', function() { setAudience('all_employees'); });
document.getElementById('aud-specific').addEventListener('change', function() { setAudience('specific_employees'); });

document.querySelectorAll('label[for^="aud-"]').forEach(function(lbl) {
    lbl.addEventListener('click', function() {
        var input = document.querySelector(this.getAttribute('for') ? '#' + this.getAttribute('for') : null);
    });
});

// Click on the card divs
document.getElementById('card-all').parentElement.addEventListener('click', function() {
    document.getElementById('aud-all').checked = true;
    setAudience('all_employees');
});
document.getElementById('card-specific').parentElement.addEventListener('click', function() {
    document.getElementById('aud-specific').checked = true;
    setAudience('specific_employees');
});

// Employee search
document.getElementById('employee-search').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    var branchFilter = document.getElementById('branch-filter') ? document.getElementById('branch-filter').value : '';
    filterEmployees(q, branchFilter);
});

@if($branches->count() > 1)
document.getElementById('branch-filter').addEventListener('change', function() {
    var q = document.getElementById('employee-search').value.toLowerCase();
    filterEmployees(q, this.value);
});
@endif

function filterEmployees(q, branchId) {
    document.querySelectorAll('.employee-item').forEach(function(item) {
        var nameMatch = item.dataset.name.includes(q);
        var branchMatch = !branchId || item.dataset.branch == branchId;
        item.style.display = (nameMatch && branchMatch) ? '' : 'none';
    });
}

function selectAllEmployees() {
    document.querySelectorAll('.employee-checkbox').forEach(function(cb) {
        if (cb.closest('.employee-item').style.display !== 'none') cb.checked = true;
    });
    updateEmpCount();
}
function deselectAllEmployees() {
    document.querySelectorAll('.employee-checkbox').forEach(function(cb) { cb.checked = false; });
    updateEmpCount();
}
function updateEmpCount() {
    var n = document.querySelectorAll('.employee-checkbox:checked').length;
    document.getElementById('emp-selected-count').textContent = n + ' selected';
}
document.querySelectorAll('.employee-checkbox').forEach(function(cb) {
    cb.addEventListener('change', updateEmpCount);
});
updateEmpCount();
</script>
@endsection
