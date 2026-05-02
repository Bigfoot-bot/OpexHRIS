@extends('tenant.employee.layouts.app')

@section('page-title', 'My Training')
@section('page-subtitle', 'Your training programs and CPD points')

@section('content')

    @php
        $totalCpd = $enrollments->where('status', 'completed')->sum('cpd_points_earned');
        $statusColors = [
            'enrolled'  => 'bg-blue-50 text-blue-600',
            'attended'  => 'bg-amber-50 text-amber-600',
            'completed' => 'bg-emerald-50 text-emerald-600',
            'cancelled' => 'bg-red-50 text-red-500',
        ];
        $typeLabels = [
            'internal'   => 'Internal',
            'external'   => 'External',
            'online'     => 'Online',
            'conference' => 'Conference',
            'workshop'   => 'Workshop',
        ];
        $categoryLabels = [
            'clinical'       => 'Clinical',
            'administrative' => 'Administrative',
            'compliance'     => 'Compliance',
            'leadership'     => 'Leadership',
            'technical'      => 'Technical',
            'soft_skills'    => 'Soft Skills',
        ];
    @endphp

    {{-- CPD Summary --}}
    <div class="bg-white rounded-xl border border-blue-100 p-5 mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-400 mb-1">Total CPD Points Earned</p>
            <p class="text-3xl font-medium text-blue-900">{{ number_format($totalCpd) }}</p>
        </div>
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
    </div>

    @if($enrollments->isEmpty())
        <div class="bg-white rounded-xl border border-blue-100 text-center py-16">
            <p class="text-gray-400 text-sm">No training programs enrolled yet.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($enrollments as $enrollment)
            @php $prog = $enrollment->trainingProgram; @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">

                {{-- Header row --}}
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-emerald-900 truncate">{{ $prog->title }}</h3>
                        @if($prog->provider)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $prog->provider }}</p>
                        @endif
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$enrollment->status] ?? 'bg-gray-50 text-gray-500' }} capitalize flex-shrink-0">
                        {{ $enrollment->status }}
                    </span>
                </div>

                {{-- Info grid --}}
                <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-gray-500">
                            {{ $prog->start_date->format('M d, Y') }}
                            @if($prog->start_date->ne($prog->end_date))
                                &rarr; {{ $prog->end_date->format('M d, Y') }}
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-gray-500">
                            @if($prog->type === 'online')
                                Online
                            @else
                                {{ $prog->location ?: 'Location TBC' }}
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span class="text-gray-500">{{ $typeLabels[$prog->type] ?? ucfirst($prog->type) }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span class="text-gray-500">{{ $categoryLabels[$prog->category] ?? ucfirst($prog->category) }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        <span class="text-gray-500">{{ $prog->cpd_points }} CPD pts</span>
                    </div>

                    @if($enrollment->cpd_points_earned)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-emerald-600 font-medium">{{ $enrollment->cpd_points_earned }} pts earned</span>
                    </div>
                    @endif

                    @if($enrollment->score)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                        <span class="text-gray-500">Score: {{ $enrollment->score }}%</span>
                    </div>
                    @endif

                    @if($enrollment->certificate_issued)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="text-amber-600 font-medium">Certificate Issued</span>
                    </div>
                    @endif
                </div>

                {{-- Description --}}
                @if($prog->description)
                    <p class="text-xs text-gray-400 mb-4 leading-relaxed">{{ $prog->description }}</p>
                @endif

                {{-- Meeting link button for online trainings --}}
                @if($prog->type === 'online' && $prog->meeting_link)
                    <a href="{{ $prog->meeting_link }}" target="_blank"
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Join Meeting
                    </a>
                @endif

            </div>
            @endforeach
        </div>

        @if($enrollments->hasPages())
            <div class="mt-4">{{ $enrollments->links() }}</div>
        @endif
    @endif

@endsection
