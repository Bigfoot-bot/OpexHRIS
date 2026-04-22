@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Leave Request')
@section('page-subtitle', $leaveRequest->employee->full_name . ' — ' . $leaveRequest->leaveType->name)

@section('page-actions')
    <a href="{{ route('tenant.leave-requests.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Leave Requests
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

<div class="grid grid-cols-3 gap-5">

    {{-- Left Column --}}
    <div class="col-span-2 space-y-5">

        {{-- Request Details --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Request Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Employee</p>
                    <p class="text-sm text-gray-700">{{ $leaveRequest->employee->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Job Title</p>
                    <p class="text-sm text-gray-700">{{ $leaveRequest->employee->job_title }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Leave Type</p>
                    <p class="text-sm text-gray-700">{{ $leaveRequest->leaveType->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Days Requested</p>
                    <p class="text-sm text-gray-700">{{ $leaveRequest->days_requested }} {{ $leaveRequest->is_half_day ? '(Half Day)' : '' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Start Date</p>
                    <p class="text-sm text-gray-700">{{ $leaveRequest->start_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">End Date</p>
                    <p class="text-sm text-gray-700">{{ $leaveRequest->end_date->format('M d, Y') }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-1">Reason</p>
                    <p class="text-sm text-gray-700">{{ $leaveRequest->reason ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Rejection Reason --}}
        @if($leaveRequest->status === 'rejected' && $leaveRequest->rejection_reason)
        <div class="bg-red-50 rounded-xl border border-red-100 p-6">
            <h2 class="text-sm font-medium text-red-700 mb-2">Rejection Reason</h2>
            <p class="text-sm text-red-600">{{ $leaveRequest->rejection_reason }}</p>
        </div>
        @endif

        {{-- Reject Form --}}
        @if($leaveRequest->status === 'pending')
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Reject Request</h2>
            <form method="POST" action="{{ route('tenant.leave-requests.reject', $leaveRequest) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Rejection Reason *</label>
                    <textarea name="rejection_reason" rows="3" required
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                              placeholder="Provide reason for rejection..."></textarea>
                </div>
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Reject Request
                </button>
            </form>
        </div>
        @endif

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Status Card --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Status</h2>
            @php
                $statusColors = [
                    'pending'   => 'bg-amber-50 text-amber-600',
                    'approved'  => 'bg-emerald-50 text-emerald-600',
                    'rejected'  => 'bg-red-50 text-red-500',
                    'cancelled' => 'bg-gray-50 text-gray-500',
                ];
            @endphp
            <span class="text-xs px-3 py-1.5 rounded-full {{ $statusColors[$leaveRequest->status] ?? '' }} capitalize">
                {{ $leaveRequest->status }}
            </span>

            @if($leaveRequest->approved_at)
                <div class="mt-4">
                    <p class="text-xs text-gray-400 mb-1">Approved At</p>
                    <p class="text-sm text-gray-700">{{ $leaveRequest->approved_at->format('M d, Y H:i') }}</p>
                </div>
            @endif

            @if($leaveRequest->status === 'pending')
                <div class="mt-5 pt-5 border-t border-gray-50">
                    <form method="POST" action="{{ route('tenant.leave-requests.approve', $leaveRequest) }}">
                        @csrf
                        <button type="submit"
                                class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                            Approve Request
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Submitted --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Submitted</h2>
            <p class="text-sm text-gray-600">{{ $leaveRequest->created_at->format('M d, Y H:i') }}</p>
        </div>

    </div>

</div>

@endsection
