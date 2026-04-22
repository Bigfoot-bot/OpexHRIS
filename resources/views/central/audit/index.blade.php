@extends('central.layouts.app')

@section('page-title', 'Audit Logs')
@section('page-subtitle', 'Track all system activities across facilities')

@section('content')

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-green-100 p-5 mb-6">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="flex gap-3 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search description..."
                   class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 w-48"/>

            <select name="module" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Modules</option>
                @foreach($modules as $module)
                    <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>{{ $module }}</option>
                @endforeach
            </select>

            <select name="action" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>

            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.audit-logs.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600 py-2">Clear</a>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white rounded-xl border border-green-100">
        @if($logs->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No audit logs found.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Action</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Module</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Description</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            @php
                                $actionColors = [
                                    'created'  => 'bg-emerald-50 text-emerald-600',
                                    'updated'  => 'bg-blue-50 text-blue-600',
                                    'deleted'  => 'bg-red-50 text-red-500',
                                    'approved' => 'bg-teal-50 text-teal-600',
                                    'rejected' => 'bg-amber-50 text-amber-600',
                                    'invited'  => 'bg-purple-50 text-purple-600',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $actionColors[$log->action] ?? 'bg-gray-50 text-gray-500' }} capitalize">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $log->module }}</td>
                        <td class="px-6 py-3 text-sm text-gray-700 max-w-xs truncate">{{ $log->description }}</td>
                        <td class="px-6 py-3 text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
