@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', $applicant->full_name)
@section('page-subtitle', 'Applicant for ' . $applicant->jobPosition->title)

@section('page-actions')
    <a href="{{ route('tenant.positions.show', $applicant->jobPosition) }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Position
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

        {{-- Applicant Info --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Applicant Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Full Name</p>
                    <p class="text-sm text-gray-700">{{ $applicant->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Email</p>
                    <p class="text-sm text-gray-700">{{ $applicant->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Phone</p>
                    <p class="text-sm text-gray-700">{{ $applicant->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Current Employer</p>
                    <p class="text-sm text-gray-700">{{ $applicant->current_employer ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Current Position</p>
                    <p class="text-sm text-gray-700">{{ $applicant->current_position ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Years of Experience</p>
                    <p class="text-sm text-gray-700">{{ $applicant->years_of_experience }} years</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Highest Qualification</p>
                    <p class="text-sm text-gray-700">{{ $applicant->highest_qualification ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Applied On</p>
                    <p class="text-sm text-gray-700">{{ $applicant->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Cover Letter --}}
        @if($applicant->cover_letter)
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-3">Cover Letter</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $applicant->cover_letter }}</p>
        </div>
        @endif

        {{-- Notes --}}
        @if($applicant->notes)
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-3">Internal Notes</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $applicant->notes }}</p>
        </div>
        @endif

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Stage --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Application Stage</h2>

            @php
                $stageColors = [
                    'applied'     => 'bg-gray-50 text-gray-500',
                    'shortlisted' => 'bg-blue-50 text-blue-600',
                    'interview'   => 'bg-amber-50 text-amber-600',
                    'assessment'  => 'bg-purple-50 text-purple-600',
                    'offer'       => 'bg-teal-50 text-teal-600',
                    'hired'       => 'bg-emerald-50 text-emerald-600',
                    'rejected'    => 'bg-red-50 text-red-500',
                ];
            @endphp

            <span class="text-xs px-3 py-1.5 rounded-full {{ $stageColors[$applicant->stage] ?? '' }} capitalize">
                {{ $applicant->stage }}
            </span>

            @if($applicant->interview_date)
                <div class="mt-3">
                    <p class="text-xs text-gray-400">Interview Date</p>
                    <p class="text-sm text-gray-700">{{ $applicant->interview_date->format('M d, Y') }}</p>
                </div>
            @endif

            @if($applicant->score)
                <div class="mt-3">
                    <p class="text-xs text-gray-400">Score</p>
                    <p class="text-sm text-gray-700">{{ $applicant->score }}/100</p>
                </div>
            @endif

            {{-- Update Stage Form --}}
            <form method="POST" action="{{ route('tenant.applicants.stage', $applicant) }}" class="mt-4 pt-4 border-t border-gray-50">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Move to Stage</label>
                        <select name="stage"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="applied" {{ $applicant->stage === 'applied' ? 'selected' : '' }}>Applied</option>
                            <option value="shortlisted" {{ $applicant->stage === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                            <option value="interview" {{ $applicant->stage === 'interview' ? 'selected' : '' }}>Interview</option>
                            <option value="assessment" {{ $applicant->stage === 'assessment' ? 'selected' : '' }}>Assessment</option>
                            <option value="offer" {{ $applicant->stage === 'offer' ? 'selected' : '' }}>Offer</option>
                            <option value="hired" {{ $applicant->stage === 'hired' ? 'selected' : '' }}>Hired</option>
                            <option value="rejected" {{ $applicant->stage === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Interview Date</label>
                        <input type="date" name="interview_date" value="{{ $applicant->interview_date?->format('Y-m-d') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Score (0-100)</label>
                        <input type="number" name="score" value="{{ $applicant->score }}" min="0" max="100"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                        <textarea name="notes" rows="2"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ $applicant->notes }}</textarea>
                    </div>
                    <button type="submit"
                            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                        Update Stage
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

@endsection
