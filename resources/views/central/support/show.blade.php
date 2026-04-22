@extends('central.layouts.app')

@section('page-title', 'Support Ticket')
@section('page-subtitle', '#' . $ticket->id . ' — ' . $ticket->subject)

@section('page-actions')
    <a href="{{ route('admin.support.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ? Back to Tickets
    </a>
@endsection

@section('content')

    <div class="grid grid-cols-3 gap-5">

        {{-- Ticket Details --}}
        <div class="col-span-2 space-y-5">

            {{-- Message --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-medium text-emerald-900">{{ $ticket->subject }}</h2>
                    <span class="text-xs text-gray-400">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $ticket->message }}</p>
            </div>

            {{-- Admin Reply --}}
            @if($ticket->admin_reply)
            <div class="bg-emerald-50 rounded-xl border border-emerald-100 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-emerald-900">Admin Reply</h3>
                    <span class="text-xs text-gray-400">{{ $ticket->replied_at->format('M d, Y H:i') }}</span>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $ticket->admin_reply }}</p>
            </div>
            @endif

            {{-- Reply Form --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h3 class="text-sm font-medium text-emerald-900 mb-4">
                    {{ $ticket->admin_reply ? 'Update Reply' : 'Send Reply' }}
                </h3>
                <form method="POST" action="{{ route('admin.support.reply', $ticket) }}">
                    @csrf
                    <textarea name="admin_reply" rows="4" required
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-3"
                              placeholder="Type your reply here...">{{ $ticket->admin_reply }}</textarea>
                    <button type="submit"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                        Send Reply
                    </button>
                </form>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Ticket Info --}}
            <div class="bg-white rounded-xl border border-green-100 p-5">
                <h3 class="text-sm font-medium text-emerald-900 mb-4">Ticket Info</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400">Facility</p>
                        <p class="text-sm text-gray-700">{{ $ticket->tenant->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Category</p>
                        <p class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $ticket->category) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Priority</p>
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $ticket->priorityColor }} capitalize">
                            {{ $ticket->priority }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Status</p>
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $ticket->statusColor }} capitalize">
                            {{ str_replace('_', ' ', $ticket->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Created</p>
                        <p class="text-sm text-gray-700">{{ $ticket->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="bg-white rounded-xl border border-green-100 p-5">
                <h3 class="text-sm font-medium text-emerald-900 mb-4">Update Status</h3>
                <form method="POST" action="{{ route('admin.support.status', $ticket) }}">
                    @csrf
                    <select name="status" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-3">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    <button type="submit"
                            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                        Update Status
                    </button>
                </form>
            </div>

        </div>

    </div>

@endsection
