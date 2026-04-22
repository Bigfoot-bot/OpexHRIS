@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Audit Logs')
@section('page-subtitle', 'Track all actions performed in the system')

@section('content')

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-green-100 p-4 mb-6">
        <form method="GET" action="{{ route('tenant.audit.index') }}" class="flex items-end gap-3">
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search actions..."
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Module</label>
                <select name="module"
                        class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>
                            {{ $module }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Action</label>
                <select name="action"
                        class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'module', 'action', 'date']))
                <a href="{{ route('tenant.audit.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 py-2">Clear</a>
            @endif
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($logs->isEmpty())
            <div class="text-center py-16">
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-sm">No audit logs yet.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Time</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">User</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Module</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Action</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Description</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            <p class="text-xs text-gray-600">{{ $log->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <p class="text-sm text-gray-700">{{ $log->user_name ?? 'System' }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2.5 py-1 rounded-full bg-blue-50 text-blue-600">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            @php
                                $actionColors = [
                                    'created'  => 'bg-emerald-50 text-emerald-600',
                                    'updated'  => 'bg-blue-50 text-blue-600',
                                    'deleted'  => 'bg-red-50 text-red-500',
                                    'approved' => 'bg-teal-50 text-teal-600',
                                    'rejected' => 'bg-amber-50 text-amber-600',
                                    'login'    => 'bg-purple-50 text-purple-600',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $actionColors[$log->action] ?? 'bg-gray-50 text-gray-500' }} capitalize">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <p class="text-sm text-gray-600">{{ $log->description }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs text-gray-400 font-mono">{{ $log->ip_address }}</span>
                        </td>
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
