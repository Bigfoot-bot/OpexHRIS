@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Reports & Analytics')
@section('page-subtitle', 'Insights across all HR modules')

@section('content')

<div class="grid grid-cols-3 gap-5">

    <a href="{{ route('tenant.reports.headcount') }}"
       class="bg-white rounded-xl border border-green-100 p-6 hover:border-emerald-300 hover:shadow-sm transition-all">
        <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h2 class="text-sm font-medium text-emerald-900 mb-1">Headcount Report</h2>
        <p class="text-xs text-gray-400">Staff distribution by department, type and gender</p>
    </a>

    <a href="{{ route('tenant.reports.payroll') }}"
       class="bg-white rounded-xl border border-green-100 p-6 hover:border-emerald-300 hover:shadow-sm transition-all">
        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-sm font-medium text-emerald-900 mb-1">Payroll Report</h2>
        <p class="text-xs text-gray-400">Salary costs, deductions and payroll trends</p>
    </a>

    <a href="{{ route('tenant.reports.leave') }}"
       class="bg-white rounded-xl border border-green-100 p-6 hover:border-emerald-300 hover:shadow-sm transition-all">
        <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <h2 class="text-sm font-medium text-emerald-900 mb-1">Leave Report</h2>
        <p class="text-xs text-gray-400">Leave patterns, approvals and utilization</p>
    </a>

    <a href="{{ route('tenant.reports.compliance') }}"
       class="bg-white rounded-xl border border-green-100 p-6 hover:border-emerald-300 hover:shadow-sm transition-all">
        <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h2 class="text-sm font-medium text-emerald-900 mb-1">Compliance Report</h2>
        <p class="text-xs text-gray-400">License status, expiries and disciplinary cases</p>
    </a>

    <a href="{{ route('tenant.reports.training') }}"
       class="bg-white rounded-xl border border-green-100 p-6 hover:border-emerald-300 hover:shadow-sm transition-all">
        <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h2 class="text-sm font-medium text-emerald-900 mb-1">Training Report</h2>
        <p class="text-xs text-gray-400">CPD points, enrollments and completions</p>
    </a>

</div>

@endsection
