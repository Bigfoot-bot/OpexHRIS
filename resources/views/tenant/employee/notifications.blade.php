@extends('tenant.employee.layouts.app')

@section('page-title', 'My Notifications')
@section('page-subtitle', 'Your recent notifications')

@section('content')

    <div class="bg-white rounded-xl border border-blue-100">

        @if($notifications->isEmpty())
            <div class="text-center py-16">
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-sm">No notifications yet.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($notifications as $notification)
                <div class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50/50">
                    @php
                        $typeBg = [
                            'success' => 'bg-emerald-100 text-emerald-600',
                            'warning' => 'bg-amber-100 text-amber-600',
                            'danger'  => 'bg-red-100 text-red-600',
                            'info'    => 'bg-blue-100 text-blue-600',
                        ];
                    @endphp
                    <div class="w-9 h-9 rounded-full {{ $typeBg[$notification->type] ?? 'bg-blue-100 text-blue-600' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-blue-900">{{ $notification->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $notification->message }}</p>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection