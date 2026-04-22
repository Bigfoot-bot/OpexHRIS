@extends('tenant.layouts.app')
@section('page-title', 'PIP Details')
@section('page-subtitle', $pip->title)
@section('page-actions')
    @if($pip->status === 'draft')
        <form method="POST" action="{{ route('tenant.pip.activate', $pip) }}" class="inline">@csrf<button type="submit" style="background-color:#064e3b;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Activate PIP</button></form>
    @endif
    @if($pip->status === 'active')
        <form method="POST" action="{{ route('tenant.pip.complete', $pip) }}" class="inline">@csrf<button type="submit" style="background-color:#064e3b;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Mark Completed</button></form>
    @endif
    <a href="{{ route('tenant.pip.edit', $pip) }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Edit</a>
    <a href="{{ route('tenant.pip.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Back</a>
@endsection
@section('content')
<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Reason for PIP</p>
                <p class="text-sm text-gray-700">{{ $pip->reason }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Goals & Expectations</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $pip->goals }}</p>
            </div>
            @if($pip->support_provided)
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Support Provided</p>
                <p class="text-sm text-gray-700">{{ $pip->support_provided }}</p>
            </div>
            @endif
            @if($pip->progress_notes)
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Progress Notes</p>
                <p class="text-sm text-gray-700">{{ $pip->progress_notes }}</p>
            </div>
            @endif
            @if($pip->outcome)
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Outcome</p>
                <p class="text-sm text-gray-700">{{ $pip->outcome }}</p>
            </div>
            @endif
        </div>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">PIP Details</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-400">Employee</span><span class="font-medium">{{ $pip->employee->first_name ?? 'N/A' }} {{ $pip->employee->last_name ?? '' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Start Date</span><span>{{ $pip->start_date->format('M d, Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">End Date</span><span>{{ $pip->end_date->format('M d, Y') }}</span></div>
                @if($pip->review_date)<div class="flex justify-between"><span class="text-gray-400">Review Date</span><span>{{ $pip->review_date->format('M d, Y') }}</span></div>@endif
                <div class="flex justify-between"><span class="text-gray-400">Status</span>
                    @php $colors = ['draft' => 'text-gray-600', 'active' => 'text-amber-600', 'completed' => 'text-emerald-700', 'extended' => 'text-blue-600', 'terminated' => 'text-red-600']; @endphp
                    <span class="font-medium {{ $colors[$pip->status] }} capitalize">{{ $pip->status }}</span>
                </div>
                <div class="flex justify-between"><span class="text-gray-400">Duration</span><span>{{ $pip->start_date->diffInDays($pip->end_date) }} days</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
