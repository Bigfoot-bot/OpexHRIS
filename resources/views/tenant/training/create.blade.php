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

        {{-- Program Details --}}
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
                            <option value="internal"   {{ old('type') === 'internal'   ? 'selected' : '' }}>Internal</option>
                            <option value="external"   {{ old('type') === 'external'   ? 'selected' : '' }}>External</option>
                            <option value="online"     {{ old('type') === 'online'     ? 'selected' : '' }}>Online</option>
                            <option value="conference" {{ old('type') === 'conference' ? 'selected' : '' }}>Conference</option>
                            <option value="workshop"   {{ old('type') === 'workshop'   ? 'selected' : '' }}>Workshop</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Category *</label>
                        <select name="category" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="clinical"        {{ old('category') === 'clinical'        ? 'selected' : '' }}>Clinical</option>
                            <option value="administrative"  {{ old('category') === 'administrative'  ? 'selected' : '' }}>Administrative</option>
                            <option value="compliance"      {{ old('category') === 'compliance'      ? 'selected' : '' }}>Compliance</option>
                            <option value="leadership"      {{ old('category') === 'leadership'      ? 'selected' : '' }}>Leadership</option>
                            <option value="technical"       {{ old('category') === 'technical'       ? 'selected' : '' }}>Technical</option>
                            <option value="soft_skills"     {{ old('category') === 'soft_skills'     ? 'selected' : '' }}>Soft Skills</option>
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

                {{-- Meeting link — shown only when type = online --}}
                <div id="meeting-link-wrapper" style="{{ old('type') === 'online' ? '' : 'display:none;' }}">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Meeting Link <span class="text-gray-400">(Zoom / Google Meet)</span></label>
                    <input type="url" name="meeting_link" value="{{ old('meeting_link') }}"
                           placeholder="https://zoom.us/j/xxx  or  https://meet.google.com/xxx"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
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

        {{-- Enrollment --}}
        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Enroll Employees</h2>

            {{-- Audience cards --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-600 mb-2">Who to enroll *</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="audience" value="all_employees" id="aud-all"
                               {{ old('audience', 'all_employees') === 'all_employees' ? 'checked' : '' }}
                               style="position:absolute;opacity:0;width:0;height:0;">
                        <div id="card-all" class="border-2 rounded-xl p-3 text-center transition-all {{ old('audience', 'all_employees') === 'all_employees' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <svg class="w-5 h-5 mx-auto mb-1 {{ old('audience', 'all_employees') === 'all_employees' ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-xs font-semibold text-gray-700">All Employees</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $employees->count() }} active</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="audience" value="by_department" id="aud-dept"
                               {{ old('audience') === 'by_department' ? 'checked' : '' }}
                               style="position:absolute;opacity:0;width:0;height:0;">
                        <div id="card-dept" class="border-2 rounded-xl p-3 text-center transition-all {{ old('audience') === 'by_department' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <svg class="w-5 h-5 mx-auto mb-1 {{ old('audience') === 'by_department' ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-xs font-semibold text-gray-700">By Department</p>
                            <p class="text-xs text-gray-400 mt-0.5">All in a dept.</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="audience" value="specific_employees" id="aud-specific"
                               {{ old('audience') === 'specific_employees' ? 'checked' : '' }}
                               style="position:absolute;opacity:0;width:0;height:0;">
                        <div id="card-specific" class="border-2 rounded-xl p-3 text-center transition-all {{ old('audience') === 'specific_employees' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <svg class="w-5 h-5 mx-auto mb-1 {{ old('audience') === 'specific_employees' ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="text-xs font-semibold text-gray-700">Specific Staff</p>
                            <p class="text-xs text-gray-400 mt-0.5">Choose one or more</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Department picker --}}
            <div id="department-picker" style="{{ old('audience') === 'by_department' ? '' : 'display:none;' }}">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Select Department *</label>
                <select name="department" id="department-select"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white appearance-none"
                        style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:16px;padding-right:2.5rem;">
                    <option value="">-- Select a department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
                @if(empty($departments))
                    <p class="text-xs text-amber-600 mt-1">No departments configured. Go to <a href="{{ route('tenant.settings.index') }}" class="underline">Settings → Departments</a> to add departments first.</p>
                @endif
            </div>

            {{-- Employee picker --}}
            <div id="employee-picker" style="{{ old('audience') === 'specific_employees' ? '' : 'display:none;' }}" class="space-y-2">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Select Employees *</label>

                @if($branches->count() > 1)
                <select id="branch-filter" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-2">
                    <option value="">All branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                @endif

                <input type="text" id="employee-search" placeholder="Search employees..."
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-2"/>

                <div class="flex gap-3 mb-2">
                    <button type="button" onclick="selectAllEmployees()"
                            class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Select All</button>
                    <button type="button" onclick="deselectAllEmployees()"
                            class="text-xs text-gray-400 hover:text-gray-600">Clear</button>
                    <span id="emp-selected-count" class="text-xs text-gray-400 ml-auto">0 selected</span>
                </div>

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
                            <p class="text-xs text-gray-400 truncate">{{ $emp->job_title ?? $emp->department ?? '' }}{{ $emp->branch ? ' — ' . $emp->branch->name : '' }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                Create &amp; Enroll
            </button>
            <a href="{{ route('tenant.training.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
        </div>

    </form>
</div>

<script>
// Meeting link toggle
var typeSelect = document.querySelector('[name="type"]');
function toggleMeetingLink() {
    document.getElementById('meeting-link-wrapper').style.display =
        typeSelect.value === 'online' ? '' : 'none';
    if (typeSelect.value !== 'online') {
        document.querySelector('[name="meeting_link"]').value = '';
    }
}
typeSelect.addEventListener('change', toggleMeetingLink);

function setAudience(value) {
    var isSpecific = value === 'specific_employees';
    var isDept     = value === 'by_department';

    document.getElementById('employee-picker').style.display   = isSpecific ? '' : 'none';
    document.getElementById('department-picker').style.display = isDept ? '' : 'none';
    if (!isSpecific) deselectAllEmployees();
    if (!isDept) document.getElementById('department-select').value = '';

    var active   = 'border-2 rounded-xl p-3 text-center transition-all border-emerald-500 bg-emerald-50';
    var inactive = 'border-2 rounded-xl p-3 text-center transition-all border-gray-200 hover:border-gray-300';
    document.getElementById('card-all').className      = (value === 'all_employees') ? active : inactive;
    document.getElementById('card-dept').className     = isDept     ? active : inactive;
    document.getElementById('card-specific').className = isSpecific ? active : inactive;
}

document.getElementById('aud-all').addEventListener('change',      function() { setAudience('all_employees'); });
document.getElementById('aud-dept').addEventListener('change',     function() { setAudience('by_department'); });
document.getElementById('aud-specific').addEventListener('change', function() { setAudience('specific_employees'); });

document.getElementById('card-all').parentElement.addEventListener('click', function() {
    document.getElementById('aud-all').checked = true;
    setAudience('all_employees');
});
document.getElementById('card-dept').parentElement.addEventListener('click', function() {
    document.getElementById('aud-dept').checked = true;
    setAudience('by_department');
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
        var nameMatch   = item.dataset.name.includes(q);
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
