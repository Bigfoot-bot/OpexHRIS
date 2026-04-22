@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Payroll Report')
@section('page-subtitle', 'Salary costs, deductions and payroll trends')

@section('page-actions')
    <a href="{{ route('tenant.exports.payroll') }}"
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
    <div class="grid grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Gross Paid</p>
            <p class="text-xl font-medium text-emerald-900">KES {{ number_format($stats['total_gross']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Net Paid</p>
            <p class="text-xl font-medium text-emerald-900">KES {{ number_format($stats['total_net']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total PAYE</p>
            <p class="text-xl font-medium text-red-500">KES {{ number_format($stats['total_paye']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Avg Basic Salary</p>
            <p class="text-xl font-medium text-emerald-900">KES {{ number_format($stats['avg_salary']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-5 mb-5">

        {{-- Statutory Deductions --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Total Statutory Deductions</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">PAYE</span>
                    <span class="text-sm font-medium text-red-500">KES {{ number_format($stats['total_paye']) }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">NHIF</span>
                    <span class="text-sm font-medium text-red-500">KES {{ number_format($stats['total_nhif']) }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">NSSF (Employee)</span>
                    <span class="text-sm font-medium text-red-500">KES {{ number_format($stats['total_nssf']) }}</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-gray-600">Housing Levy</span>
                    <span class="text-sm font-medium text-red-500">KES {{ number_format($stats['total_housing']) }}</span>
                </div>
            </div>
        </div>

        {{-- Payroll History --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Payroll History</h2>
            @if($periods->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">No payroll runs yet.</p>
            @else
                <div class="space-y-2">
                    @foreach($periods as $period)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div>
                            <p class="text-sm text-gray-700">{{ $period->name }}</p>
                            <p class="text-xs text-gray-400">{{ $period->records->count() }} employees</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-emerald-900">KES {{ number_format($period->records->sum('net_salary')) }}</p>
                            @php
                                $statusColors = [
                                    'draft'    => 'bg-gray-50 text-gray-500',
                                    'approved' => 'bg-emerald-50 text-emerald-600',
                                    'paid'     => 'bg-teal-50 text-teal-600',
                                ];
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$period->status] ?? '' }} capitalize">
                                {{ $period->status }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Top Earners --}}
    @if($currentPeriod)
    <div class="bg-white rounded-xl border border-green-100">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-medium text-emerald-900">Top Earners — {{ $currentPeriod->name }}</h2>
        </div>
        @if($topEarners->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">No payroll records yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">#</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Basic</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Gross</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Deductions</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Net Pay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($topEarners as $index => $record)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3 text-sm text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-3">
                            <p class="text-sm font-medium text-emerald-900">{{ $record->employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $record->employee->job_title }}</p>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ number_format($record->basic_salary) }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ number_format($record->gross_salary) }}</td>
                        <td class="px-6 py-3 text-sm text-red-500">{{ number_format($record->total_deductions) }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-emerald-900">{{ number_format($record->net_salary) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @endif

@endsection
