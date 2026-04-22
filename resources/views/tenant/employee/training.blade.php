@extends('tenant.employee.layouts.app')

@section('page-title', 'My Training')
@section('page-subtitle', 'Your training programs and CPD points')

@section('content')

    {{-- CPD Summary --}}
    @php
        $totalCpd = $enrollments->where('status', 'completed')->sum('cpd_points_earned');
    @endphp
    <div class="bg-white rounded-xl border border-blue-100 p-5 mb-6">
        <div class="flex items-center justify-between">
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
    </div>

    <div class="bg-white rounded-xl border border-blue-100">
        @if($enrollments->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No training programs enrolled yet.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Program</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">CPD Points</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Score</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Certificate</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            <p class="text-sm font-medium text-gray-700">{{ $enrollment->trainingProgram->title }}</p>
                            <p class="text-xs text-gray-400">{{ $enrollment->trainingProgram->provider ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600 capitalize">
                            {{ str_replace('_', ' ', $enrollment->trainingProgram->training_type) }}
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm font-medium text-blue-700">{{ $enrollment->cpd_points_earned }} pts</span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">
                            {{ $enrollment->score ? $enrollment->score . '%' : '—' }}
                        </td>
                        <td class="px-6 py-3 text-sm">
                            @if($enrollment->certificate_number)
                                <span class="text-xs text-emerald-600">{{ $enrollment->certificate_number }}</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @php
                                $colors = [
                                    'enrolled'  => 'bg-blue-50 text-blue-600',
                                    'attended'  => 'bg-amber-50 text-amber-600',
                                    'completed' => 'bg-emerald-50 text-emerald-600',
                                    'cancelled' => 'bg-red-50 text-red-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$enrollment->status] ?? '' }} capitalize">
                                {{ $enrollment->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($enrollments->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $enrollments->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection