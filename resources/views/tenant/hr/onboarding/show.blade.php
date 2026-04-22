@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Onboarding — ' . $employee->full_name)
@section('page-subtitle', $employee->job_title . ' · ' . $employee->department)

@section('page-actions')
    <a href="{{ route('tenant.employees.show', $employee) }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Employee
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Progress --}}
    <div class="bg-white rounded-xl border border-green-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-sm font-medium text-emerald-900">Onboarding Progress</h2>
                <p class="text-xs text-gray-400">{{ $stats['completed'] }} of {{ $stats['total'] }} tasks completed</p>
            </div>
            <span class="text-2xl font-medium {{ $stats['percent'] === 100 ? 'text-emerald-600' : 'text-gray-700' }}">
                {{ $stats['percent'] }}%
            </span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3">
            <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500"
                 style="width: {{ $stats['percent'] }}%"></div>
        </div>
        @if($stats['percent'] === 100)
            <p class="text-xs text-emerald-600 mt-2 font-medium">🎉 Onboarding complete!</p>
        @endif
    </div>

    <div class="grid grid-cols-3 gap-5">

        {{-- Checklist --}}
        <div class="col-span-2 space-y-4">

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

            @foreach($byCategory as $category => $items)
            <div class="bg-white rounded-xl border border-green-100">
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
                        <form method="POST" action="{{ route('tenant.onboarding.toggle', $item) }}" class="mt-0.5">
                            @csrf
                            <button type="submit"
                                    class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors
                                    {{ $item->is_completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-300 hover:border-emerald-400' }}">
                                @if($item->is_completed)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </button>
                        </form>
                        <div class="flex-1">
                            <p class="text-sm {{ $item->is_completed ? 'line-through text-gray-400' : 'text-gray-700' }}">
                                {{ $item->title }}
                            </p>
                            @if($item->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->description }}</p>
                            @endif
                            @if($item->is_completed && $item->completed_at)
                                <p class="text-xs text-emerald-600 mt-0.5">
                                    Completed {{ $item->completed_at->format('M d, Y') }}
                                    @if($item->completedBy) by {{ $item->completedBy->name }} @endif
                                </p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('tenant.onboarding.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-300 hover:text-red-400 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>

        {{-- Right Column --}}
        <div class="space-y-5">

            {{-- Stats --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-4">Summary</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Total Tasks</span>
                        <span class="text-sm font-medium text-gray-700">{{ $stats['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Completed</span>
                        <span class="text-sm font-medium text-emerald-600">{{ $stats['completed'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Pending</span>
                        <span class="text-sm font-medium text-amber-600">{{ $stats['pending'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Employee Info --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-4">Employee</h2>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-sm font-medium">
                        {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-emerald-900">{{ $employee->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $employee->employee_number }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-400">Hire Date</p>
                        <p class="text-sm text-gray-700">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Department</p>
                        <p class="text-sm text-gray-700">{{ $employee->department ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Add Item --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-4">Add Item</h2>
                <form method="POST" action="{{ route('tenant.onboarding.store', $employee) }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Title *</label>
                            <input type="text" name="title" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                   placeholder="e.g. Collect ID copy"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Category *</label>
                            <select name="category" required
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="documentation">Documentation</option>
                                <option value="it_setup">IT Setup</option>
                                <option value="training">Training</option>
                                <option value="introduction">Introduction</option>
                                <option value="compliance">Compliance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <button type="submit"
                                class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                            Add Item
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

@endsection
