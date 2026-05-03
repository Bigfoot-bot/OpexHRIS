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
            'administrative' => 'Admin',
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
        <div class="bg-white rounded-xl border border-gray-100 text-center py-16">
            <p class="text-gray-400 text-sm">No training programs enrolled yet.</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Program</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Dates</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">CPD</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Score</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($enrollments as $enrollment)
                    @php $prog = $enrollment->trainingProgram; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">

                        {{-- Program --}}
                        <td class="px-4 py-3.5">
                            <p class="font-medium text-gray-800 leading-tight">{{ $prog->title }}</p>
                            @if($prog->provider)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $prog->provider }}</p>
                            @endif
                            <span class="text-xs text-gray-400">{{ $typeLabels[$prog->type] ?? ucfirst($prog->type) }}</span>
                        </td>

                        {{-- Category --}}
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-gray-500">{{ $categoryLabels[$prog->category] ?? ucfirst($prog->category) }}</span>
                        </td>

                        {{-- Dates --}}
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <p class="text-xs text-gray-600">{{ $prog->start_date->format('d M Y') }}</p>
                            @if($prog->start_date->ne($prog->end_date))
                                <p class="text-xs text-gray-400">to {{ $prog->end_date->format('d M Y') }}</p>
                            @endif
                        </td>

                        {{-- CPD Points --}}
                        <td class="px-4 py-3.5 text-center">
                            @if($enrollment->cpd_points_earned)
                                <span class="font-semibold text-emerald-700">{{ $enrollment->cpd_points_earned }}</span>
                                <span class="text-xs text-gray-400"> / {{ $prog->cpd_points }}</span>
                            @else
                                <span class="text-xs text-gray-400">{{ $prog->cpd_points }}</span>
                            @endif
                        </td>

                        {{-- Score --}}
                        <td class="px-4 py-3.5 text-center">
                            @if($enrollment->score)
                                <span class="text-sm font-medium text-gray-700">{{ $enrollment->score }}%</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$enrollment->status] ?? 'bg-gray-50 text-gray-500' }} capitalize font-medium">
                                {{ $enrollment->status }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                @if($prog->meeting_link)
                                    <a href="{{ $prog->meeting_link }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium px-3 py-1.5 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Join
                                    </a>
                                @endif
                                @if($enrollment->certificate_issued && $prog->certificate_provided)
                                    <a href="{{ route('tenant.training.certificate', $enrollment) }}"
                                       class="inline-flex items-center gap-1 text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 font-medium px-3 py-1.5 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Certificate
                                    </a>
                                @endif
                                @if(!$prog->meeting_link && !($enrollment->certificate_issued && $prog->certificate_provided))
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
            <div class="mt-4">{{ $enrollments->links() }}</div>
        @endif
    @endif

@endsection
