@extends('tenant.layouts.app')
@section('page-title', 'Timesheets')
@section('page-subtitle', 'Track weekly work hours')
@section('page-actions')
    <a href="{{ route('tenant.timesheets.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Timesheet</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Pending Approval</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['approved'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Approved</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_hours'], 1) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Hours</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex gap-3">
            <form method="GET" class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
        @if($timesheets->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No timesheets yet.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Week</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Total Hours</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Overtime</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($timesheets as $ts)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $ts->employee->first_name ?? 'N/A' }} {{ $ts->employee->last_name ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ts->week_start->format('M d') }} - {{ $ts->week_end->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ts->total_hours }}hrs</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ts->overtime_hours }}hrs</td>
                        <td class="px-6 py-4">
                            @php $colors = ['draft' => 'bg-gray-100 text-gray-600', 'submitted' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-red-50 text-red-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$ts->status] }} capitalize">{{ $ts->status }}</span>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="{{ route('tenant.timesheets.show', $ts) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>
                            @if($ts->status === 'submitted')
                            <form method="POST" action="{{ route('tenant.timesheets.approve', $ts) }}">@csrf<button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Approve</button></form>
                            <form method="POST" action="{{ route('tenant.timesheets.reject', $ts) }}">@csrf<button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Reject</button></form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $timesheets->links() }}</div>
        @endif
    </div>
</div>
@endsection
