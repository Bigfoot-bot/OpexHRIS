@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', $performance->employee->full_name . ' — Performance Review')
@section('page-subtitle', $performance->review_period . ' ' . $performance->review_year . ' · ' . str_replace('_', ' ', $performance->review_type))

@section('page-actions')
    <a href="{{ route('tenant.performance.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Performance
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

        {{-- Review Form --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Review Assessment</h2>

            <form method="POST" action="{{ route('tenant.performance.update', $performance) }}">
                @csrf

                <div class="space-y-4">

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Status</label>
                        <select name="status"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="draft" {{ $performance->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="self_assessment" {{ $performance->status === 'self_assessment' ? 'selected' : '' }}>Self Assessment</option>
                            <option value="manager_review" {{ $performance->status === 'manager_review' ? 'selected' : '' }}>Manager Review</option>
                            <option value="completed" {{ $performance->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    {{-- Ratings --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Self Rating (1-5)</label>
                            <input type="number" name="self_rating" value="{{ old('self_rating', $performance->self_rating) }}"
                                   min="1" max="5" step="0.1"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Manager Rating (1-5)</label>
                            <input type="number" name="manager_rating" value="{{ old('manager_rating', $performance->manager_rating) }}"
                                   min="1" max="5" step="0.1"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Final Rating (1-5)</label>
                            <input type="number" name="final_rating" value="{{ old('final_rating', $performance->final_rating) }}"
                                   min="1" max="5" step="0.1"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                    </div>

                    {{-- Self Assessment --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Self Assessment</label>
                        <textarea name="self_assessment" rows="3"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Employee's self assessment...">{{ old('self_assessment', $performance->self_assessment) }}</textarea>
                    </div>

                    {{-- Manager Comments --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Manager Comments</label>
                        <textarea name="manager_comments" rows="3"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Manager's comments...">{{ old('manager_comments', $performance->manager_comments) }}</textarea>
                    </div>

                    {{-- Strengths --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Strengths</label>
                        <textarea name="strengths" rows="2"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Key strengths...">{{ old('strengths', $performance->strengths) }}</textarea>
                    </div>

                    {{-- Areas for Improvement --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Areas for Improvement</label>
                        <textarea name="areas_for_improvement" rows="2"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Areas needing improvement...">{{ old('areas_for_improvement', $performance->areas_for_improvement) }}</textarea>
                    </div>

                    {{-- Goals Next Period --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Goals for Next Period</label>
                        <textarea name="goals_next_period" rows="2"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Goals for next review period...">{{ old('goals_next_period', $performance->goals_next_period) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Review Date</label>
                        <input type="date" name="review_date" value="{{ old('review_date', $performance->review_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>

                    <button type="submit"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                        Save Review
                    </button>

                </div>
            </form>
        </div>

        {{-- Goals --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-medium text-emerald-900">Goals</h2>
            </div>

            {{-- Add Goal Form --}}
            <form method="POST" action="{{ route('tenant.performance.goals.store') }}" class="mb-5 p-4 bg-gray-50 rounded-lg">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $performance->employee_id }}">
                <input type="hidden" name="year" value="{{ $performance->review_year }}">

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Goal Title *</label>
                        <input type="text" name="title" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="e.g. Complete CPD hours"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Category *</label>
                        <select name="category" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="performance">Performance</option>
                            <option value="learning">Learning</option>
                            <option value="behavioral">Behavioral</option>
                            <option value="project">Project</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Target Value</label>
                        <input type="number" name="target_value"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="e.g. 40"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Unit</label>
                        <input type="text" name="measurement_unit"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="e.g. hours, %, units"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label>
                        <input type="date" name="due_date"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Weight (%)</label>
                        <input type="number" name="weight" min="0" max="100"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="e.g. 20"/>
                    </div>
                </div>
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium px-4 py-2 rounded-lg">
                    Add Goal
                </button>
            </form>

            {{-- Goals List --}}
            @if($goals->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">No goals set yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($goals as $goal)
                    <div class="border border-gray-100 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="text-sm font-medium text-emerald-900">{{ $goal->title }}</p>
                                <p class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $goal->category) }}
                                    @if($goal->target_value) · Target: {{ $goal->target_value }} {{ $goal->measurement_unit }} @endif
                                    @if($goal->due_date) · Due: {{ $goal->due_date->format('M d, Y') }} @endif
                                </p>
                            </div>
                            @php
                                $goalColors = [
                                    'completed'   => 'bg-emerald-50 text-emerald-600',
                                    'in_progress' => 'bg-blue-50 text-blue-600',
                                    'cancelled'   => 'bg-red-50 text-red-500',
                                    'not_started' => 'bg-gray-50 text-gray-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $goalColors[$goal->status] ?? '' }} capitalize">
                                {{ str_replace('_', ' ', $goal->status) }}
                            </span>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>Progress</span>
                                <span>{{ $goal->progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $goal->progress }}%"></div>
                            </div>
                        </div>

                        {{-- Update Form --}}
                        <form method="POST" action="{{ route('tenant.performance.goals.update', $goal) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="number" name="progress" value="{{ $goal->progress }}" min="0" max="100"
                                   class="w-20 px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                   placeholder="%"/>
                            <select name="status"
                                    class="px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <option value="not_started" {{ $goal->status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                <option value="in_progress" {{ $goal->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ $goal->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $goal->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <button type="submit"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs px-3 py-1 rounded-lg">
                                Update
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Employee Info --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Employee</h2>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-sm font-medium">
                    {{ strtoupper(substr($performance->employee->first_name, 0, 1) . substr($performance->employee->last_name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-emerald-900">{{ $performance->employee->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $performance->employee->job_title }}</p>
                </div>
            </div>
            <div class="space-y-2">
                <div>
                    <p class="text-xs text-gray-400">Department</p>
                    <p class="text-sm text-gray-700">{{ $performance->employee->department ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Employment Status</p>
                    <p class="text-sm text-gray-700 capitalize">{{ $performance->employee->employment_status }}</p>
                </div>
            </div>
        </div>

        {{-- Rating Summary --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Rating Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-xs text-gray-400">Self Rating</span>
                    <span class="text-sm text-gray-700">{{ $performance->self_rating ?? '—' }}/5</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-400">Manager Rating</span>
                    <span class="text-sm text-gray-700">{{ $performance->manager_rating ?? '—' }}/5</span>
                </div>
                <div class="flex justify-between border-t border-gray-50 pt-3">
                    <span class="text-xs font-medium text-gray-600">Final Rating</span>
                    <span class="text-sm font-medium text-emerald-900">{{ $performance->final_rating ?? '—' }}/5</span>
                </div>
                @if($performance->final_rating)
                    <div class="bg-emerald-50 rounded-lg p-3 text-center">
                        <p class="text-sm font-medium text-emerald-700">{{ $performance->rating_label }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

@endsection
