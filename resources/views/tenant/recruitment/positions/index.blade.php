@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Recruitment')
@section('page-subtitle', 'Manage job positions and applicants')

@section('page-actions')
    <a href="{{ route('tenant.positions.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + New Position
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Positions</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Open</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Draft</p>
            <p class="text-2xl font-medium text-gray-500">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Closed</p>
            <p class="text-2xl font-medium text-red-500">{{ $stats['closed'] }}</p>
        </div>
    </div>

    {{-- Positions Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($positions->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No job positions yet.</p>
                <a href="{{ route('tenant.positions.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Create first position →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Position</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Department</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Vacancies</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Applicants</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Closing Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($positions as $position)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $position->title }}</p>
                            <p class="text-xs text-gray-400">{{ $position->location ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $position->department }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $position->type) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $position->vacancies }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-emerald-700">{{ $position->applicants_count }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-400">{{ $position->closing_date ? $position->closing_date->format('M d, Y') : '—' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'draft'   => 'bg-gray-50 text-gray-500',
                                    'open'    => 'bg-emerald-50 text-emerald-600',
                                    'closed'  => 'bg-red-50 text-red-500',
                                    'on_hold' => 'bg-amber-50 text-amber-600',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$position->status] ?? '' }} capitalize">
                                {{ str_replace('_', ' ', $position->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.positions.show', $position) }}"
                                   class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                <a href="{{ route('tenant.positions.edit', $position) }}"
                                   class="text-xs text-blue-500 hover:text-blue-700">Edit</a>
                                <form method="POST" action="{{ route('tenant.positions.destroy', $position) }}"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($positions->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $positions->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
