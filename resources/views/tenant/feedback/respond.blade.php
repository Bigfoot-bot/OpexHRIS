@extends('tenant.layouts.app')
@section('page-title', 'Submit Feedback')
@section('page-subtitle', $feedback->title)
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="mb-6 p-4 bg-emerald-50 rounded-xl">
            <p class="text-sm text-emerald-700">You are providing feedback for <strong>{{ $feedback->employee->first_name ?? '' }} {{ $feedback->employee->last_name ?? '' }}</strong></p>
            @if($response->is_anonymous)<p class="text-xs text-emerald-600 mt-1">This feedback is anonymous</p>@endif
        </div>
        <form method="POST" action="{{ route('tenant.feedback.submit', $response) }}" class="space-y-6">
            @csrf
            <div>
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Ratings (1 = Poor, 5 = Excellent)</h3>
                <div class="space-y-4">
                    @foreach(['rating_overall' => 'Overall Performance', 'rating_communication' => 'Communication Skills', 'rating_teamwork' => 'Teamwork & Collaboration', 'rating_technical' => 'Technical Skills', 'rating_leadership' => 'Leadership'] as $field => $label)
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-gray-700">{{ $label }} *</label>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="{{ $field }}" value="{{ $i }}" required class="sr-only peer"/>
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-sm font-medium text-gray-600 peer-checked:bg-emerald-700 peer-checked:text-white peer-checked:border-emerald-700 hover:bg-emerald-50">{{ $i }}</span>
                            </label>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Key Strengths</label>
                <textarea name="strengths" rows="3" placeholder="What does this employee do well?" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Areas for Improvement</label>
                <textarea name="improvements" rows="3" placeholder="What could this employee improve?" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Additional Comments</label>
                <textarea name="comments" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2 rounded-lg">Submit Feedback</button>
                <a href="{{ route('tenant.feedback.index') }}" class="bg-gray-100 text-gray-600 text-sm font-medium px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
