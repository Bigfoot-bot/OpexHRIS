@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('title', 'Announcements')

@section('page-title', 'Announcements')
@section('page-subtitle', 'Announcements from OpEx HRIS and your facility')

@section('page-actions')
    @if(auth()->user()->portal_preference === 'hr' || auth()->user()->portal_preference === 'admin')
        <a href="{{ route('tenant.announcements.create') }}"
           class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + New Announcement
        </a>
    @endif
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- Global Announcements from Super Admin --}}
    @if($globalAnnouncements->isNotEmpty())
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-widest mb-3">From OpEx HRIS</h2>
            <div class="space-y-4">
                @foreach($globalAnnouncements as $announcement)
                    <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-0.5 rounded-full">OpEx HRIS</span>
                            <span class="text-xs text-gray-400">{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ $announcement->title }}</h3>
                        <p class="text-sm text-gray-500 mb-3">{{ $announcement->body }}</p>
                        @if($announcement->meeting_link)
                            <a href="{{ $announcement->meeting_link }}" target="_blank"
                               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                </svg>
                                Join Meeting
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Facility Announcements --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-widest mb-3">Facility Announcements</h2>
        @if($announcements->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm">No facility announcements yet.</p>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('tenant.announcements.create') }}"
                       class="inline-block mt-4 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                        Create Announcement
                    </a>
                @endif
            </div>
        @else
            <div class="space-y-4">
                @foreach($announcements as $announcement)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded-full">Facility</span>
                                    <span class="text-xs text-gray-400">{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ $announcement->title }}</h3>
                                <p class="text-sm text-gray-500 mb-3">{{ $announcement->body }}</p>
                                @if($announcement->meeting_link)
                                    <a href="{{ $announcement->meeting_link }}" target="_blank"
                                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                        </svg>
                                        Join Meeting
                                    </a>
                                @endif
                            </div>
                            @if(auth()->user()->portal_preference === 'hr' || auth()->user()->portal_preference === 'admin')
                                <form method="POST" action="{{ route('tenant.announcements.destroy', $announcement) }}"
                                      onsubmit="return confirm('Delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $announcements->links() }}</div>
        @endif
    </div>

</div>
@endsection





