@extends('tenant.layouts.app')
@section('page-title', '360 Feedback Results')
@section('page-subtitle', $feedback->title)
@section('page-actions')
    <a href="{{ route('tenant.feedback.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Back</a>
@endsection
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-5 gap-4">
        @foreach(['overall' => 'Overall', 'communication' => 'Communication', 'teamwork' => 'Teamwork', 'technical' => 'Technical', 'leadership' => 'Leadership'] as $key => $label)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-emerald-600">{{ $avgRatings[$key] ?? '-' }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $label }}</p>
            <div class="flex justify-center gap-0.5 mt-2">
                @for($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= round($avgRatings[$key]) ? 'text-amber-400' : 'text-gray-200' }} text-sm">*</span>
                @endfor
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="text-sm font-semibold text-gray-800">Reviewer Responses ({{ $feedback->completed_reviews }}/{{ $feedback->total_reviewers }})</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($feedback->responses as $response)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-800">
                            {{ $response->is_anonymous ? 'Anonymous Reviewer' : ($response->reviewer->name ?? 'Unknown') }}
                        </p>
                        @if($response->is_submitted)
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">Submitted</span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-600">Pending</span>
                        @endif
                    </div>
                    @if($response->reviewer_id === auth()->id() && !$response->is_submitted)
                    <a href="{{ route('tenant.feedback.respond', $response) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium mt-1 inline-block">Give Feedback</a>
                    @endif
                    @if($response->is_submitted)
                    <div class="grid grid-cols-5 gap-2 text-xs text-gray-500 mt-2">
                        <div>Overall: <strong>{{ $response->rating_overall }}/5</strong></div>
                        <div>Comm: <strong>{{ $response->rating_communication }}/5</strong></div>
                        <div>Team: <strong>{{ $response->rating_teamwork }}/5</strong></div>
                        <div>Tech: <strong>{{ $response->rating_technical }}/5</strong></div>
                        <div>Lead: <strong>{{ $response->rating_leadership }}/5</strong></div>
                    </div>
                    @if($response->strengths)
                    <p class="text-xs text-gray-600 mt-2"><strong>Strengths:</strong> {{ $response->strengths }}</p>
                    @endif
                    @if($response->improvements)
                    <p class="text-xs text-gray-600 mt-1"><strong>Areas for improvement:</strong> {{ $response->improvements }}</p>
                    @endif
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Feedback Details</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-400">Employee</span><span>{{ $feedback->employee->first_name ?? 'N/A' }} {{ $feedback->employee->last_name ?? '' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Type</span><span class="capitalize">{{ str_replace('_', ' ', $feedback->type) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Due Date</span><span>{{ $feedback->due_date ? $feedback->due_date->format('M d, Y') : '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="capitalize">{{ str_replace('_', ' ', $feedback->status) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Progress</span><span>{{ $feedback->completed_reviews }}/{{ $feedback->total_reviewers }} completed</span></div>
            </div>
            @if($feedback->description)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Description</p>
                <p class="text-sm text-gray-600">{{ $feedback->description }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

