@extends('tenant.employee.layouts.app')

@section('page-title', 'My Performance')
@section('page-subtitle', 'Your performance reviews and ratings')

@section('content')

    <div class="bg-white rounded-xl border border-blue-100">
        @if($reviews->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No performance reviews yet.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Period</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Rating</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($reviews as $review)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            <p class="text-sm font-medium text-blue-900">{{ $review->review_period }} {{ $review->review_year }}</p>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600 capitalize">
                            {{ str_replace('_', ' ', $review->review_type) }}
                        </td>
                        <td class="px-6 py-3">
                            @if($review->final_rating)
                                <span class="text-sm font-medium text-emerald-700">{{ $review->final_rating }}/5</span>
                            @else
                                <span class="text-xs text-gray-400">Not rated yet</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @php
                                $statusColors = [
                                    'draft'     => 'bg-gray-50 text-gray-500',
                                    'submitted' => 'bg-blue-50 text-blue-600',
                                    'completed' => 'bg-emerald-50 text-emerald-600',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$review->status] ?? '' }} capitalize">
                                {{ $review->status }}
                            </span>
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
