@extends('tenant.branch.layout')
@section('page-title', 'Announcements')
@section('content')
<div class="grid grid-cols-2 gap-6">
    {{-- Post Announcement Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Post Announcement</h2>
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('tenant.branch.announcements.store', $branch) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Title *</label>
                <input type="text" name="title" required placeholder="Announcement title" value="{{ old('title') }}"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Content *</label>
                <textarea name="content" required rows="4" placeholder="Write your announcement..."
                          class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('content') }}</textarea>
            </div>

            {{-- Audience --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-2">Send To *</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer p-2.5 rounded-lg border {{ old('audience', 'branch_only') === 'branch_only' ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200' }} hover:bg-gray-50 transition-colors audience-option" data-value="branch_only">
                        <input type="radio" name="audience" value="branch_only" class="w-4 h-4 text-emerald-600 branch-audience-radio"
                               {{ old('audience', 'branch_only') === 'branch_only' ? 'checked' : '' }}>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">All Branch Employees</p>
                            <p class="text-xs text-gray-400">Visible to all employees in {{ $branch->name }}</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer p-2.5 rounded-lg border {{ old('audience') === 'specific_employees' ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200' }} hover:bg-gray-50 transition-colors audience-option" data-value="specific_employees">
                        <input type="radio" name="audience" value="specific_employees" class="w-4 h-4 text-emerald-600 branch-audience-radio"
                               {{ old('audience') === 'specific_employees' ? 'checked' : '' }}>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Specific Employees</p>
                            <p class="text-xs text-gray-400">Choose individual employees in this branch</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Employee Picker --}}
            <div id="branch-employee-picker" style="{{ old('audience') === 'specific_employees' ? '' : 'display:none;' }}" class="space-y-2">
                <input type="text" id="branch-emp-search" placeholder="Search employees..."
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                <div class="flex gap-3 mb-1">
                    <button type="button" onclick="branchSelectAll()" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Select All</button>
                    <button type="button" onclick="branchClearAll()" class="text-xs text-gray-400 hover:text-gray-600">Clear</button>
                    <span id="branch-emp-count" class="text-xs text-gray-400 ml-auto">0 selected</span>
                </div>
                <div class="border border-gray-200 rounded-xl divide-y divide-gray-100 max-h-52 overflow-y-auto">
                    @forelse($branchEmployees as $emp)
                    <label class="branch-emp-item flex items-center gap-3 px-3 py-2 hover:bg-gray-50 cursor-pointer"
                           data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}">
                        <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}"
                               class="branch-emp-checkbox w-4 h-4 text-emerald-600 rounded border-gray-300"
                               {{ in_array($emp->id, old('employee_ids', [])) ? 'checked' : '' }}>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-700 truncate">{{ $emp->first_name }} {{ $emp->last_name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $emp->job_title ?? $emp->department ?? '' }}</p>
                        </div>
                    </label>
                    @empty
                    <div class="px-3 py-4 text-center text-xs text-gray-400">No active employees in this branch.</div>
                    @endforelse
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Post Announcement
            </button>
        </form>
    </div>

    {{-- Announcements List --}}
    <div class="space-y-4">
        <h2 class="text-sm font-semibold text-gray-800">Announcements</h2>
        @if($announcements->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                <p class="text-gray-400 text-sm">No announcements yet.</p>
            </div>
        @else
            @foreach($announcements as $ann)
            @php
                $empCount = null;
                $empNames = null;
                if ($ann->employee_id !== null) {
                    $siblings = \App\Models\Announcement::where('tenant_id', $ann->tenant_id)
                        ->where('title', $ann->title)->where('body', $ann->body)
                        ->whereNotNull('employee_id')
                        ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT(?, '%Y-%m-%d %H:%i')", [$ann->created_at])
                        ->with('employee')->get();
                    $empCount = $siblings->count();
                    $empNames = $siblings->pluck('employee')->filter()->map(fn($e) => $e->first_name . ' ' . $e->last_name);
                }
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            @if($ann->employee_id !== null)
                                <span class="text-xs bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full">{{ $empCount }} {{ $empCount === 1 ? 'employee' : 'employees' }}</span>
                            @else
                                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">This Branch</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $ann->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ $ann->title }}</h3>
                        @if($empNames?->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($empNames as $name)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded-full">{{ $name }}</span>
                                @endforeach
                            </div>
                        @endif
                        <p class="text-sm text-gray-600 mt-2">{{ $ann->body }}</p>
                    </div>
                    @if($ann->branch_id === $branch->id && $ann->employee_id === null)
                    <form method="POST" action="{{ route('tenant.branch.announcements.destroy', [$branch, $ann]) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 ml-3">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
            <div>{{ $announcements->links() }}</div>
        @endif
    </div>
</div>

<script>
document.querySelectorAll('.branch-audience-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.audience-option').forEach(function(opt) {
            opt.classList.remove('border-emerald-400', 'bg-emerald-50');
            opt.classList.add('border-gray-200');
        });
        this.closest('.audience-option').classList.add('border-emerald-400', 'bg-emerald-50');
        this.closest('.audience-option').classList.remove('border-gray-200');
        document.getElementById('branch-employee-picker').style.display =
            this.value === 'specific_employees' ? '' : 'none';
        if (this.value !== 'specific_employees') branchClearAll();
    });
});
var searchEl = document.getElementById('branch-emp-search');
if (searchEl) {
    searchEl.addEventListener('input', function() {
        var q = this.value.toLowerCase();
        document.querySelectorAll('.branch-emp-item').forEach(function(item) {
            item.style.display = item.dataset.name.includes(q) ? '' : 'none';
        });
    });
}
function branchSelectAll() {
    document.querySelectorAll('.branch-emp-checkbox').forEach(function(cb) {
        if (cb.closest('.branch-emp-item').style.display !== 'none') cb.checked = true;
    });
    updateBranchCount();
}
function branchClearAll() {
    document.querySelectorAll('.branch-emp-checkbox').forEach(function(cb) { cb.checked = false; });
    updateBranchCount();
}
function updateBranchCount() {
    var n = document.querySelectorAll('.branch-emp-checkbox:checked').length;
    var el = document.getElementById('branch-emp-count');
    if (el) el.textContent = n + ' selected';
}
document.querySelectorAll('.branch-emp-checkbox').forEach(function(cb) {
    cb.addEventListener('change', updateBranchCount);
});
updateBranchCount();
</script>
@endsection
