@extends('tenant.branch.layout')
@section('page-title', 'Leave Requests')
@section('content')
<div class="space-y-4">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($leaveRequests->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No leave requests found.</p></div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Leave Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Duration</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($leaveRequests as $leave)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $leave->employee->first_name ?? 'N/A' }} {{ $leave->employee->last_name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $leave->leaveType->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @php $colors = ['pending' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-red-50 text-red-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$leave->status] ?? '' }} capitalize">{{ $leave->status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($leave->status === 'pending')
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('tenant.branch.leave.approve', [$branch, $leave]) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('tenant.branch.leave.reject', [$branch, $leave]) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Reject</button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $leaveRequests->links() }}</div>
        @endif
    </div>
</div>
@endsection
