@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Training & CPD')
@section('page-subtitle', 'Manage training programs and CPD tracking')

@section('page-actions')
    <a href="{{ route('tenant.training.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + New Program
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
            <p class="text-xs text-gray-400 mb-1">Total Programs</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Planned</p>
            <p class="text-2xl font-medium text-blue-600">{{ $stats['planned'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Ongoing</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['ongoing'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Completed</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['completed'] }}</p>
        </div>
    </div>

    {{-- Programs Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($programs->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No training programs yet.</p>
                <a href="{{ route('tenant.training.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Create first program →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Program</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Category</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">CPD Points</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Enrolled</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Dates</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($programs as $program)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $program->title }}</p>
                            <p class="text-xs text-gray-400">{{ $program->provider ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-500 capitalize">{{ $program->type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $program->category) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-emerald-700">{{ $program->cpd_points }} pts</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $program->enrollments_count }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-600">{{ $program->start_date->format('M d') }} — {{ $program->end_date->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'planned'   => 'bg-blue-50 text-blue-600',
                                    'ongoing'   => 'bg-amber-50 text-amber-600',
                                    'completed' => 'bg-emerald-50 text-emerald-600',
                                    'cancelled' => 'bg-red-50 text-red-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$program->status] ?? '' }} capitalize">
                                {{ $program->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.training.show', $program) }}"
                                   class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                <form method="POST" action="{{ route('tenant.training.destroy', $program) }}"
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

            @if($programs->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $programs->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
