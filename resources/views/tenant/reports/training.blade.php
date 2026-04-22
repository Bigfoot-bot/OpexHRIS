@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Training Report')
@section('page-subtitle', 'CPD points, enrollments and completions')

@section('page-actions')
    <a href="{{ route('tenant.reports.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Reports
    </a>
@endsection

@section('content')

    {{-- Summary Stats --}}
    <div class="grid grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Programs</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total_programs'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Enrollments</p>
            <p class="text-2xl font-medium text-blue-600">{{ $stats['total_enrollments'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Completed</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total CPD Points Earned</p>
            <p class="text-2xl font-medium text-purple-600">{{ number_format($stats['total_cpd_points']) }}</p>
        </div>
    </div>

    {{-- Top Trainees --}}
    <div class="bg-white rounded-xl border border-green-100">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-medium text-emerald-900">Most Active Trainees</h2>
        </div>
        @if($topTrainees->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">No training data yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">#</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Department</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Programs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($topTrainees as $index => $employee)
                    @if($employee->training_enrollments_count > 0)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3 text-sm text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">
                            <p class="text-sm font-medium text-emerald-900">{{ $employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $employee->job_title }}</p>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $employee->department ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="text-sm font-medium text-purple-600">{{ $employee->training_enrollments_count }} programs</span>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection
