@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Disciplinary Cases')
@section('page-subtitle', 'Manage employee disciplinary actions')

@section('page-actions')
    <a href="{{ route('tenant.disciplinary.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + New Case
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
            <p class="text-xs text-gray-400 mb-1">Total Cases</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Open</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Closed</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['closed'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">This Month</p>
            <p class="text-2xl font-medium text-gray-700">{{ $stats['this_month'] }}</p>
        </div>
    </div>

    {{-- Cases Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($cases->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No disciplinary cases yet.</p>
                <a href="{{ route('tenant.disciplinary.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Create first case →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Case No.</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Title</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Severity</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Incident Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($cases as $case)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono text-gray-500">{{ $case->case_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $case->employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $case->employee->job_title }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">{{ $case->title }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $typeColors = [
                                    'verbal_warning'  => 'bg-blue-50 text-blue-600',
                                    'written_warning' => 'bg-amber-50 text-amber-600',
                                    'final_warning'   => 'bg-orange-50 text-orange-600',
                                    'suspension'      => 'bg-red-50 text-red-500',
                                    'termination'     => 'bg-red-50 text-red-700',
                                    'other'           => 'bg-gray-50 text-gray-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $typeColors[$case->type] ?? '' }} capitalize">
                                {{ str_replace('_', ' ', $case->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $severityColors = [
                                    'minor'            => 'bg-emerald-50 text-emerald-600',
                                    'moderate'         => 'bg-amber-50 text-amber-600',
                                    'serious'          => 'bg-orange-50 text-orange-600',
                                    'gross_misconduct' => 'bg-red-50 text-red-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $severityColors[$case->severity] ?? '' }} capitalize">
                                {{ str_replace('_', ' ', $case->severity) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'open'                => 'bg-amber-50 text-amber-600',
                                    'under_investigation' => 'bg-blue-50 text-blue-600',
                                    'hearing_scheduled'   => 'bg-purple-50 text-purple-600',
                                    'closed'              => 'bg-emerald-50 text-emerald-600',
                                    'appealed'            => 'bg-red-50 text-red-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$case->status] ?? '' }} capitalize">
                                {{ str_replace('_', ' ', $case->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-400">{{ $case->incident_date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.disciplinary.show', $case) }}"
                                   class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                <form method="POST" action="{{ route('tenant.disciplinary.destroy', $case) }}"
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

            @if($cases->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $cases->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
