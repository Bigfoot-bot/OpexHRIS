@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Leave Requests')
@section('page-subtitle', 'Manage all leave applications')

@section('page-actions')
    <a href="{{ route('tenant.exports.leave-requests') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50">Export Excel</a>
    <a href="{{ route('tenant.leave-requests.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + New Request
    </a>
@endsection

@section('content')

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-green-100 p-4 mb-4">
        <form method="GET" class="flex gap-3 flex-wrap">
            @if(isset($branches) && $branches->count() > 0)
            <select name="branch_id" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Branches</option>
                @foreach($branches as $br)
                    <option value="{{ $br->id }}" {{ request('branch_id') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                @endforeach
            </select>
            @endif
            <select name="status" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" class="bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
            @if(request()->hasAny(['branch_id', 'status']))
            <a href="{{ route('tenant.leave-requests.index') }}" class="bg-gray-100 text-gray-600 text-sm px-4 py-2 rounded-lg">Clear</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-green-100">

        @if($leaveRequests->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No leave requests yet.</p>
                <a href="{{ route('tenant.leave-requests.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Submit first request →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Leave Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">From</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">To</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Days</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($leaveRequests as $request)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $request->employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $request->employee->job_title }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $request->leaveType->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $request->start_date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $request->end_date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $request->days_requested }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending'   => 'bg-amber-50 text-amber-600',
                                    'approved'  => 'bg-emerald-50 text-emerald-600',
                                    'rejected'  => 'bg-red-50 text-red-500',
                                    'cancelled' => 'bg-gray-50 text-gray-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$request->status] ?? '' }} capitalize">
                                {{ $request->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.leave-requests.show', $request) }}"
                                   class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                @if($request->status === 'pending')
                                    <form method="POST" action="{{ route('tenant.leave-requests.approve', $request) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-500 hover:text-blue-700">Approve</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('tenant.leave-requests.destroy', $request) }}"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($leaveRequests->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $leaveRequests->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
