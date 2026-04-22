@extends('central.layouts.app')

@section('title', 'New Announcement')

@section('page-title', 'New Announcement')
@section('page-subtitle', 'Send an announcement to all active facilities')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">

        @if($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Announcement title"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Message *</label>
                    <textarea name="body" rows="6" required
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                              placeholder="Write your announcement here...">{{ old('body') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Meeting Link <span class="text-gray-400">(optional)</span></label>
                    <input type="url" name="meeting_link" value="{{ old('meeting_link') }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="https://meet.google.com/xxx or https://zoom.us/j/xxx"/>
                    <p class="text-xs text-gray-400 mt-1">Add a Zoom, Google Meet or any other meeting link</p>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="send_email" id="send_email" value="1" checked
                           class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"/>
                    <label for="send_email" class="text-sm text-gray-600">Send email notification to all facility admins</label>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Publish Announcement
                </button>
                <a href="{{ route('admin.announcements.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
