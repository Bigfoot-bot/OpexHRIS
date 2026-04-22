@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', $position->title)
@section('page-subtitle', $position->department . ' · ' . str_replace('_', ' ', $position->type))

@section('page-actions')
    <a href="{{ route('tenant.applicants.create', $position) }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + Add Applicant
    </a>
    <a href="{{ route('tenant.positions.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back
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

        {{-- Pipeline --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Application Pipeline</h2>
            <div class="grid grid-cols-7 gap-2">
                @php
                    $stages = [
                        'applied'     => ['label' => 'Applied', 'color' => 'bg-gray-100 text-gray-600'],
                        'shortlisted' => ['label' => 'Shortlisted', 'color' => 'bg-blue-100 text-blue-600'],
                        'interview'   => ['label' => 'Interview', 'color' => 'bg-amber-100 text-amber-600'],
                        'assessment'  => ['label' => 'Assessment', 'color' => 'bg-purple-100 text-purple-600'],
                        'offer'       => ['label' => 'Offer', 'color' => 'bg-teal-100 text-teal-600'],
                        'hired'       => ['label' => 'Hired', 'color' => 'bg-emerald-100 text-emerald-600'],
                        'rejected'    => ['label' => 'Rejected', 'color' => 'bg-red-100 text-red-500'],
                    ];
                @endphp
                @foreach($stages as $key => $stage)
                <div class="text-center">
                    <div class="{{ $stage['color'] }} rounded-lg p-3 mb-1">
                        <p class="text-xl font-medium">{{ $stageStats[$key] }}</p>
                    </div>
                    <p class="text-xs text-gray-400">{{ $stage['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Applicants --}}
        <div class="bg-white rounded-xl border border-green-100">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <h2 class="text-sm font-medium text-emerald-900">Applicants ({{ $position->applicants->count() }})</h2>
            </div>

            @if($position->applicants->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-400 text-sm">No applicants yet.</p>
                    <a href="{{ route('tenant.applicants.create', $position) }}"
                       class="inline-block mt-2 text-sm text-emerald-600 hover:text-emerald-800">
                        Add first applicant →
                    </a>
                </div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Applicant</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Experience</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Stage</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Applied</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($position->applicants as $applicant)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <p class="text-sm font-medium text-emerald-900">{{ $applicant->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $applicant->email }}</p>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm text-gray-600">{{ $applicant->years_of_experience }} yrs</span>
                            </td>
                            <td class="px-6 py-3">
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
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $stageColors[$applicant->stage] ?? '' }} capitalize">
                                    {{ $applicant->stage }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-xs text-gray-400">{{ $applicant->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('tenant.applicants.show', $applicant) }}"
                                       class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                    <form method="POST" action="{{ route('tenant.applicants.destroy', $applicant) }}"
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
            @endif
        </div>

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Position Info --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Position Info</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    @php
                        $statusColors = [
                            'draft'   => 'bg-gray-50 text-gray-500',
                            'open'    => 'bg-emerald-50 text-emerald-600',
                            'closed'  => 'bg-red-50 text-red-500',
                            'on_hold' => 'bg-amber-50 text-amber-600',
                        ];
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$position->status] ?? '' }} capitalize">
                        {{ str_replace('_', ' ', $position->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Vacancies</p>
                    <p class="text-sm text-gray-700">{{ $position->vacancies }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Location</p>
                    <p class="text-sm text-gray-700">{{ $position->location ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Salary Range</p>
                    <p class="text-sm text-gray-700">
                        @if($position->salary_min && $position->salary_max)
                            KES {{ number_format($position->salary_min) }} — {{ number_format($position->salary_max) }}
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Closing Date</p>
                    <p class="text-sm text-gray-700">{{ $position->closing_date ? $position->closing_date->format('M d, Y') : '—' }}</p>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-50">
                <a href="{{ route('tenant.positions.edit', $position) }}"
                   class="block w-full text-center text-sm bg-emerald-50 text-emerald-700 hover:bg-emerald-100 py-2 rounded-lg transition-colors">
                    Edit Position
                </a>
            </div>
        </div>

        {{-- Description --}}
        @if($position->description)
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-3">Description</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $position->description }}</p>
        </div>
        @endif

        {{-- Requirements --}}
        @if($position->requirements)
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-3">Requirements</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $position->requirements }}</p>
        </div>
        @endif

    </div>

</div>

@endsection
