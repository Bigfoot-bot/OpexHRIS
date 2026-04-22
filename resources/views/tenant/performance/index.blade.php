@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Performance Management')
@section('page-subtitle', 'Track and manage employee performance reviews')

@section('page-actions')
    <a href="{{ route('tenant.performance.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + New Review
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Reviews</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total_reviews'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Completed</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Pending</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Avg Rating</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['avg_rating'] ?? '—' }}</p>
        </div>
    </div>

    {{-- Reviews Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($reviews->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No performance reviews yet.</p>
                <a href="{{ route('tenant.performance.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Create first review →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Period</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Rating</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Due Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($reviews as $review)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $review->employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $review->employee->job_title }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $review->review_period }} {{ $review->review_year }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $review->review_type) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'draft'           => 'bg-gray-50 text-gray-500',
                                    'self_assessment' => 'bg-blue-50 text-blue-600',
                                    'manager_review'  => 'bg-amber-50 text-amber-600',
                                    'completed'       => 'bg-emerald-50 text-emerald-600',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$review->status] ?? '' }} capitalize">
                                {{ str_replace('_', ' ', $review->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($review->final_rating)
                                <span class="text-sm font-medium text-emerald-900">{{ $review->final_rating }}/5</span>
                                <span class="text-xs text-gray-400 ml-1">{{ $review->rating_label }}</span>
                            @else
                                <span class="text-xs text-gray-400">Not rated</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-400">{{ $review->due_date ? $review->due_date->format('M d, Y') : '—' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.performance.show', $review) }}"
                                   class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                <form method="POST" action="{{ route('tenant.performance.destroy', $review) }}"
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

            @if($reviews->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $reviews->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
