@extends('tenant.layouts.app')
@section('page-title', '360-Degree Feedback')
@section('page-subtitle', 'Manage peer and multi-rater feedback')
@section('page-actions')
    <a href="{{ route('tenant.feedback.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Feedback Request</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['progress'] }}</p>
            <p class="text-xs text-gray-400 mt-1">In Progress</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Completed</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($feedbacks->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No feedback requests yet.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Title</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Progress</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Due Date</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($feedbacks as $fb)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $fb->employee->first_name ?? 'N/A' }} {{ $fb->employee->last_name ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $fb->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $fb->type) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ $fb->total_reviewers > 0 ? ($fb->completed_reviews / $fb->total_reviewers) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $fb->completed_reviews }}/{{ $fb->total_reviewers }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $fb->due_date ? $fb->due_date->format('M d, Y') : '-' }}</td>
                        <td class="px-6 py-4">
                            @php $colors = ['pending' => 'bg-gray-100 text-gray-600', 'in_progress' => 'bg-blue-50 text-blue-600', 'completed' => 'bg-emerald-50 text-emerald-700']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$fb->status] }} capitalize">{{ str_replace('_', ' ', $fb->status) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.feedback.show', $fb) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>
                                <form method="POST" action="{{ route('tenant.feedback.destroy', $fb) }}">@csrf @method('DELETE')<button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $feedbacks->links() }}</div>
        @endif
    </div>
</div>
@endsection

