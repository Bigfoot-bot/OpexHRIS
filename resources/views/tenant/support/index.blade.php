@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Support Tickets')
@section('page-subtitle', 'Submit and track your support requests')

@section('page-actions')
    <a href="{{ route('tenant.support.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        + New Ticket
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-green-100">
        @if($tickets->isEmpty())
            <div class="text-center py-16">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <p class="text-gray-400 text-sm">No support tickets yet.</p>
                <a href="{{ route('tenant.support.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Submit your first ticket ?
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Subject</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Category</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Priority</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Submitted</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            <p class="text-sm font-medium text-emerald-900 truncate max-w-xs">{{ $ticket->subject }}</p>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $ticket->category) }}</td>
                        <td class="px-6 py-3">
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
                        </td>
                        <td class="px-6 py-3">
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
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-3">
                            <a href="{{ route('tenant.support.show', $ticket) }}"
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

