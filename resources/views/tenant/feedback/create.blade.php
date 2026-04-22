@extends('tenant.layouts.app')
@section('page-title', 'New Feedback Request')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('tenant.feedback.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee Being Reviewed *</label>
                    <select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Feedback Type *</label>
                    <select name="type" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="peer">Peer Review</option>
                        <option value="manager">Manager Review</option>
                        <option value="subordinate">Subordinate Review</option>
                        <option value="self">Self Assessment</option>
                        <option value="client">Client Feedback</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Q2 2026 Peer Review" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Due Date</label>
                    <input type="date" name="due_date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div class="flex items-center gap-2 pt-5">
                    <input type="checkbox" name="is_anonymous" id="is_anonymous" class="rounded"/>
                    <label for="is_anonymous" class="text-sm text-gray-600">Anonymous Feedback</label>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Select Reviewers *</label>
                    <div class="border border-gray-200 rounded-lg p-3 max-h-48 overflow-y-auto space-y-2">
                        @foreach($users as $user)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="reviewers[]" value="{{ $user->id }}" class="rounded"/>
                            <span class="text-sm text-gray-700">{{ $user->name }} ({{ $user->email }})</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2 rounded-lg">Create Feedback Request</button>
                <a href="{{ route('tenant.feedback.index') }}" class="bg-gray-100 text-gray-600 text-sm font-medium px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
