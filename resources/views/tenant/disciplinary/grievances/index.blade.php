@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Grievances')
@section('page-subtitle', 'Manage employee grievances and complaints')

@section('page-actions')
    <a href="{{ route('tenant.grievances.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + File Grievance
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
            <p class="text-xs text-gray-400 mb-1">Total</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Open</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Resolved</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['resolved'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Critical</p>
            <p class="text-2xl font-medium text-red-500">{{ $stats['critical'] }}</p>
        </div>
    </div>

    {{-- Grievances Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($grievances->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No grievances filed yet.</p>
                <a href="{{ route('tenant.grievances.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    File first grievance →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Ref No.</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Title</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Category</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Priority</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Submitted</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($grievances as $grievance)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono text-gray-500">{{ $grievance->grievance_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $grievance->employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $grievance->employee->job_title }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">{{ $grievance->title }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $grievance->category) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $priorityColors = [
                                    'low'      => 'bg-gray-50 text-gray-500',
                                    'medium'   => 'bg-blue-50 text-blue-600',
                                    'high'     => 'bg-amber-50 text-amber-600',
                                    'critical' => 'bg-red-50 text-red-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $priorityColors[$grievance->priority] ?? '' }} capitalize">
                                {{ $grievance->priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'submitted'     => 'bg-gray-50 text-gray-500',
                                    'under_review'  => 'bg-blue-50 text-blue-600',
                                    'investigation' => 'bg-amber-50 text-amber-600',
                                    'resolved'      => 'bg-emerald-50 text-emerald-600',
                                    'closed'        => 'bg-gray-50 text-gray-500',
                                    'escalated'     => 'bg-red-50 text-red-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$grievance->status] ?? '' }} capitalize">
                                {{ str_replace('_', ' ', $grievance->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-400">{{ $grievance->submitted_date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.grievances.show', $grievance) }}"
                                   class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                <form method="POST" action="{{ route('tenant.grievances.destroy', $grievance) }}"
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

            @if($grievances->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $grievances->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
