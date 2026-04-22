@extends('tenant.employee.layouts.app')

@section('page-title', 'My Onboarding')
@section('page-subtitle', 'Track your onboarding progress')

@section('content')

    {{-- Progress --}}
    <div class="bg-white rounded-xl border border-blue-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-sm font-medium text-blue-900">Onboarding Progress</h2>
                <p class="text-xs text-gray-400">{{ $stats['completed'] }} of {{ $stats['total'] }} tasks completed</p>
            </div>
            <span class="text-2xl font-medium {{ $stats['percent'] === 100 ? 'text-emerald-600' : 'text-gray-700' }}">
                {{ $stats['percent'] }}%
            </span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full transition-all duration-500"
                 style="width: {{ $stats['percent'] }}%"></div>
        </div>
        @if($stats['percent'] === 100)
            <p class="text-xs text-emerald-600 mt-2 font-medium">Onboarding complete!</p>
        @else
            <p class="text-xs text-gray-400 mt-2">{{ $stats['pending'] }} tasks remaining</p>
        @endif
    </div>

    @php
        $categoryColors = [
            'documentation' => 'bg-blue-50 text-blue-600',
            'it_setup'      => 'bg-purple-50 text-purple-600',
            'training'      => 'bg-emerald-50 text-emerald-600',
            'introduction'  => 'bg-amber-50 text-amber-600',
            'compliance'    => 'bg-red-50 text-red-500',
            'other'         => 'bg-gray-50 text-gray-500',
        ];
        $categoryLabels = [
            'documentation' => 'Documentation',
            'it_setup'      => 'IT Setup',
            'training'      => 'Training',
            'introduction'  => 'Introduction',
            'compliance'    => 'Compliance',
            'other'         => 'Other',
        ];
    @endphp

    <div class="space-y-4">
        @foreach($byCategory as $category => $items)
        <div class="bg-white rounded-xl border border-blue-100">
            <div class="px-6 py-3 border-b border-gray-50 flex items-center gap-2">
                <span class="text-xs px-2.5 py-1 rounded-full {{ $categoryColors[$category] ?? '' }}">
                    {{ $categoryLabels[$category] ?? ucfirst($category) }}
                </span>
                <span class="text-xs text-gray-400">
                    {{ $items->where('is_completed', true)->count() }}/{{ $items->count() }} done
                </span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($items as $item)
                <div class="flex items-start gap-4 px-6 py-3 {{ $item->is_completed ? 'bg-gray-50/50' : '' }}">
                    {{-- Read only checkbox --}}
                    <div class="w-5 h-5 rounded border-2 flex items-center justify-center mt-0.5 flex-shrink-0
                                {{ $item->is_completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-300' }}">
                        @if($item->is_completed)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-sm {{ $item->is_completed ? 'line-through text-gray-400' : 'text-gray-700' }}">
                            {{ $item->title }}
                        </p>
                        @if($item->is_completed && $item->completed_at)
                            <p class="text-xs text-emerald-600 mt-0.5">
                                Completed {{ $item->completed_at->format('M d, Y') }}
                            </p>
                        @endif
                    </div>
                    {{-- Status badge --}}
                    @if($item->is_completed)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 flex-shrink-0">Done</span>
                    @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-50 text-gray-400 flex-shrink-0">Pending</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

@endsection
