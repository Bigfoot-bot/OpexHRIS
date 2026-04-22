@extends('tenant.layouts.app')
@section('page-title', 'Timesheet Details')
@section('page-subtitle', $timesheet->week_start->format('M d') . ' - ' . $timesheet->week_end->format('M d, Y'))
@section('page-actions')
    @if($timesheet->status === 'draft')
    <form method="POST" action="{{ route('tenant.timesheets.submit', $timesheet) }}" class="inline">
        @csrf
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Submit for Approval</button>
    </form>
    @endif
    @if($timesheet->status === 'submitted')
    <form method="POST" action="{{ route('tenant.timesheets.approve', $timesheet) }}" class="inline">
        @csrf
        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Approve</button>
    </form>
    <form method="POST" action="{{ route('tenant.timesheets.reject', $timesheet) }}" class="inline">
        @csrf
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Reject</button>
    </form>
    @endif
    <a href="{{ route('tenant.timesheets.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Back</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $timesheet->total_hours }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Hours</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $timesheet->regular_hours }}</p>
            <p class="text-xs text-gray-400 mt-1">Regular Hours</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $timesheet->overtime_hours }}</p>
            <p class="text-xs text-gray-400 mt-1">Overtime Hours</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            @php $colors = ['draft' => 'text-gray-600', 'submitted' => 'text-amber-600', 'approved' => 'text-emerald-600', 'rejected' => 'text-red-600']; @endphp
            <p class="text-2xl font-bold {{ $colors[$timesheet->status] }} capitalize">{{ $timesheet->status }}</p>
            <p class="text-xs text-gray-400 mt-1">Status</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-semibold text-gray-800">Daily Entries - {{ $timesheet->employee->first_name ?? '' }} {{ $timesheet->employee->last_name ?? '' }}</h2>
        </div>
        @if($timesheet->entries->isEmpty())
            <div class="p-8 text-center"><p class="text-gray-400 text-sm">No entries recorded.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Date</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Clock In</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Clock Out</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Hours</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Project</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($timesheet->entries as $entry)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $entry->date->format('D, M d') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $entry->clock_in }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $entry->clock_out }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $entry->hours }}hrs</td>
                        <td class="px-6 py-4"><span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 capitalize">{{ $entry->work_type }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $entry->project ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
