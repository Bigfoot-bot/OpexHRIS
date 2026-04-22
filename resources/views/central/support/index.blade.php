@extends('central.layouts.app')

@section('page-title', 'Support Tickets')
@section('page-subtitle', 'Manage facility support requests')

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Open</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">In Progress</p>
            <p class="text-2xl font-medium text-blue-600">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Resolved</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['resolved'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Urgent</p>
            <p class="text-2xl font-medium text-red-500">{{ $stats['urgent'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-green-100 p-5 mb-6">
        <form method="GET" action="{{ route('admin.support.index') }}" class="flex gap-3 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search tickets..."
                   class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 w-48"/>

            <select name="status" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>

            <select name="priority" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Priority</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>

            <select name="category" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Categories</option>
                <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>Billing</option>
                <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                <option value="feature_request" {{ request('category') === 'feature_request' ? 'selected' : '' }}>Feature Request</option>
            </select>

            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.support.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600 py-2">Clear</a>
        </form>
    </div>

    {{-- Tickets Table --}}
    <div class="bg-white rounded-xl border border-green-100">
        @if($tickets->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No support tickets yet.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Ticket</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Facility</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Category</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Priority</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Created</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            <p class="text-sm font-medium text-emerald-900 truncate max-w-xs">{{ $ticket->subject }}</p>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $ticket->tenant->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $ticket->category) }}</td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $ticket->priorityColor }} capitalize">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $ticket->statusColor }} capitalize">
                                {{ str_replace('_', ' ', $ticket->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.support.show', $ticket) }}"
                               class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $tickets->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
