@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Headcount Report')
@section('page-subtitle', 'Staff distribution and workforce analytics')

@section('page-actions')
    <a href="{{ route('tenant.exports.employees') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        Export Excel
    </a>
    <a href="{{ route('tenant.reports.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Reports
    </a>
@endsection

@section('content')

    {{-- Summary Stats --}}
    <div class="grid grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Total</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Active</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Probation</p>
            <p class="text-2xl font-medium text-blue-600">{{ $stats['probation'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Suspended</p>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['suspended'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Terminated</p>
            <p class="text-2xl font-medium text-red-500">{{ $stats['terminated'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Resigned</p>
            <p class="text-2xl font-medium text-gray-500">{{ $stats['resigned'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-5 mb-5">

        {{-- By Department --}}
        <div class="col-span-2 bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Staff by Department</h2>
            @if($byDepartment->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">No data available.</p>
            @else
                <div class="space-y-3">
                    @foreach($byDepartment as $dept)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-600">{{ $dept->department ?? 'Unassigned' }}</span>
                            <span class="text-sm font-medium text-emerald-900">{{ $dept->total }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-emerald-500 h-1.5 rounded-full"
                                 style="width: {{ $stats['total'] > 0 ? ($dept->total / $stats['total']) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- By Gender & Type --}}
        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-4">By Gender</h2>
                <div class="space-y-2">
                    @foreach($byGender as $gender)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 capitalize">{{ $gender->gender ?? 'Not specified' }}</span>
                        <span class="text-sm font-medium text-emerald-900">{{ $gender->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-4">By Employment Type</h2>
                <div class="space-y-2">
                    @foreach($byType as $type)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $type->employment_type) }}</span>
                        <span class="text-sm font-medium text-emerald-900">{{ $type->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Hires --}}
    <div class="bg-white rounded-xl border border-green-100">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-medium text-emerald-900">Recent Hires (Last 90 Days)</h2>
        </div>
        @if($recentHires->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">No recent hires.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Department</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Hire Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentHires as $employee)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            <p class="text-sm font-medium text-emerald-900">{{ $employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $employee->job_title }}</p>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $employee->department ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $employee->employment_type) }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $employee->hire_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3">
                            @php
                                $statusColors = [
                                    'active'    => 'bg-emerald-50 text-emerald-600',
                                    'probation' => 'bg-blue-50 text-blue-600',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$employee->employment_status] ?? 'bg-gray-50 text-gray-500' }} capitalize">
                                {{ $employee->employment_status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection
