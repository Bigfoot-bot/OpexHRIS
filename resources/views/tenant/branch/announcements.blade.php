@extends('tenant.branch.layout')
@section('page-title', 'Branch Announcements')
@section('content')
<div class="grid grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Post Announcement</h2>
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('tenant.branch.announcements.store', $branch) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Title *</label>
                <input type="text" name="title" required placeholder="Announcement title"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Content *</label>
                <textarea name="content" required rows="4" placeholder="Write your announcement..."
                          class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Post Announcement</button>
        </form>
    </div>

    <div class="space-y-4">
        <h2 class="text-sm font-semibold text-gray-800">Announcements</h2>
        @if($announcements->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                <p class="text-gray-400 text-sm">No announcements yet.</p>
            </div>
        @else
            @foreach($announcements as $ann)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ $ann->title }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $ann->created_at->format('M d, Y H:i') }}
                            @if($ann->branch_id)
                                <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full ml-1">Branch</span>
                            @else
                                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full ml-1">Facility-wide</span>
                            @endif
                        </p>
                    </div>
                    @if($ann->branch_id === $branch->id)
                    <form method="POST" action="{{ route('tenant.branch.announcements.destroy', [$branch, $ann]) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                    </form>
                    @endif
                </div>
                <p class="text-sm text-gray-600 mt-3">{{ $ann->body }}</p>
            </div>
            @endforeach
            <div>{{ $announcements->links() }}</div>
        @endif
    </div>
</div>
@endsection

