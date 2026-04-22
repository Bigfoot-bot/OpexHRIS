@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Support Ticket')
@section('page-subtitle', '#' . $ticket->id . ' — ' . $ticket->subject)

@section('page-actions')
    <a href="{{ route('tenant.support.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ? Back to Tickets
    </a>
@endsection

@section('content')

    <div class="grid grid-cols-3 gap-5">

        {{-- Ticket Details --}}
        <div class="col-span-2 space-y-5">

            <div class="bg-white rounded-xl border border-green-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-medium text-emerald-900">{{ $ticket->subject }}</h2>
                    <span class="text-xs text-gray-400">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $ticket->message }}</p>
            </div>

            @if($ticket->admin_reply)
            <div class="bg-emerald-50 rounded-xl border border-emerald-100 p-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-emerald-700 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs">O</span>
                        </div>
                        <h3 class="text-sm font-medium text-emerald-900">OpEx HRIS Support</h3>
                    </div>
                    <span class="text-xs text-gray-400">{{ $ticket->replied_at->format('M d, Y H:i') }}</span>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $ticket->admin_reply }}</p>
            </div>
            @else
            <div class="bg-amber-50 rounded-xl border border-amber-100 p-5 text-center">
                <p class="text-sm text-amber-600">Your ticket is open. Our team will respond shortly.</p>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-green-100 p-5">
                <h3 class="text-sm font-medium text-emerald-900 mb-4">Ticket Info</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400">Ticket ID</p>
                        <p class="text-sm text-gray-700">#{{ $ticket->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Category</p>
                        <p class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $ticket->category) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Priority</p>
                        @php
                            $priorityColors = [
                                'low'    => 'bg-gray-50 text-gray-500',
                                'medium' => 'bg-blue-50 text-blue-600',
                                'high'   => 'bg-amber-50 text-amber-600',
                                'urgent' => 'bg-red-50 text-red-500',
                            ];
                        @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $priorityColors[$ticket->priority] ?? '' }} capitalize">
                            {{ $ticket->priority }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Status</p>
                        @php
                            $statusColors = [
                                'open'        => 'bg-amber-50 text-amber-600',
                                'in_progress' => 'bg-blue-50 text-blue-600',
                                'resolved'    => 'bg-emerald-50 text-emerald-600',
                                'closed'      => 'bg-gray-50 text-gray-500',
                            ];
                        @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$ticket->status] ?? '' }} capitalize">
                            {{ str_replace('_', ' ', $ticket->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Submitted</p>
                        <p class="text-sm text-gray-700">{{ $ticket->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

