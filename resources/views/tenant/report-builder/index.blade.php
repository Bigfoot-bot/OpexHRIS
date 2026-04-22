@extends('tenant.layouts.app')
@section('page-title', 'Custom Report Builder')
@section('page-subtitle', 'Build and export custom reports')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('tenant.report-builder.generate') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Report Type *</label>
                    <select name="report_type" id="report_type" required onchange="updateFilters(this.value)"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Report</option>
                        @foreach($reportTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Date From</label>
                    <input type="date" name="date_from" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Date To</label>
                    <input type="date" name="date_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
            </div>

            {{-- Dynamic Filters --}}
            <div id="dynamic-filters" class="grid grid-cols-3 gap-4 hidden">
                <div id="filter-department" class="hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Department</label>
                    <input type="text" name="department" placeholder="e.g. Clinical" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div id="filter-status" class="hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div id="filter-employment-status" class="hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employment Status</label>
                    <select name="employment_status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="on_leave">On Leave</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Output Format *</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="format" value="view" checked class="text-emerald-600"/>
                        <span class="text-sm text-gray-700">View on screen</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="format" value="csv" class="text-emerald-600"/>
                        <span class="text-sm text-gray-700">Download CSV</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2 rounded-lg">Generate Report</button>
            </div>
        </form>
    </div>

    {{-- Quick Reports --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Quick Reports</h2>
        <div class="grid grid-cols-4 gap-4">
            @foreach($reportTypes as $key => $label)
            <form method="POST" action="{{ route('tenant.report-builder.generate') }}">
                @csrf
                <input type="hidden" name="report_type" value="{{ $key }}"/>
                <input type="hidden" name="format" value="csv"/>
                <button type="submit" class="w-full p-4 border border-gray-100 rounded-xl text-left hover:bg-emerald-50 hover:border-emerald-200 transition-colors">
                    <p class="text-sm font-medium text-gray-800">{{ $label }}</p>
                    <p class="text-xs text-gray-400 mt-1">Download CSV</p>
                </button>
            </form>
            @endforeach
        </div>
    </div>
</div>

<script>
function updateFilters(type) {
    const container = document.getElementById('dynamic-filters');
    const deptFilter = document.getElementById('filter-department');
    const statusFilter = document.getElementById('filter-status');
    const empStatusFilter = document.getElementById('filter-employment-status');

    container.classList.add('hidden');
    deptFilter.classList.add('hidden');
    statusFilter.classList.add('hidden');
    empStatusFilter.classList.add('hidden');

    if (['employees', 'headcount'].includes(type)) {
        container.classList.remove('hidden');
        deptFilter.classList.remove('hidden');
        empStatusFilter.classList.remove('hidden');
    } else if (['leave', 'overtime', 'loans', 'expenses'].includes(type)) {
        container.classList.remove('hidden');
        statusFilter.classList.remove('hidden');
    }
}
</script>
@endsection
