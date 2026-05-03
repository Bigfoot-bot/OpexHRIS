<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ tenant('name') ?? 'HRIS' }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex h-screen overflow-hidden">

    <aside class="w-56 flex flex-col flex-shrink-0" style="background-color: {{ isset($branding) ? $branding->primary_color : '#064e3b' }};">
        <div class="px-5 py-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                @if(tenant('logo'))
                    <img src="{{ asset('logos/' . tenant('logo')) }}" alt="Logo" class="w-8 h-8 object-contain rounded-lg bg-white p-0.5"/>
                @else
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                        <div class="w-4 h-4 bg-emerald-600 rounded-sm"></div>
                    </div>
                @endif
                <div>
                    <div class="text-white text-sm font-medium truncate">{{ tenant('name') }}</div>
                    <div class="text-white/40 text-xs">HRIS Portal</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.dashboard') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('app.dashboard') }}
            </a>

            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_employees'))
            <a href="{{ route('tenant.employees.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.employees.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ __('app.employees') }}
            </a>
            @endif

            @if(auth()->user()->is_admin || auth()->user()->hasPermission('view_reports') || auth()->user()->hasPermission('manage_reports'))
            <a href="{{ route('tenant.reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.reports.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Reports
            </a>
            <a href="{{ route('tenant.report-builder.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.report-builder.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Report Builder
            </a>
            <a href="{{ route('tenant.scheduled-reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.scheduled-reports.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Scheduled Reports
            </a>
            @endif







            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_recruitment'))
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Recruitment</p>
            <a href="{{ route('tenant.positions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.positions.*') || request()->routeIs('tenant.applicants.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Jobs & ATS
            </a>
            @endif

            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_leave'))
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Leave</p>
            <a href="{{ route('tenant.leave-requests.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.leave-requests.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Leave Requests
            </a>
            @if(auth()->user()->is_admin)
            <a href="{{ route('tenant.leave-types.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.leave-types.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Leave Types
            </a>
            @endif
            @endif

            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_payroll') || auth()->user()->hasPermission('view_payroll'))
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Payroll</p>
            <a href="{{ route('tenant.payroll.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.payroll.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('app.payroll') }}
            </a>
            @endif

            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_performance'))
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Performance</p>
            <a href="{{ route('tenant.performance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.performance.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ __('app.performance') }}
            </a>
            <a href="{{ route('tenant.pip.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.pip.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                PIPs
            </a>
            <a href="{{ route('tenant.feedback.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.feedback.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                360 Feedback
            </a>
            @endif

            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_employees'))
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Attendance</p>
            <a href="{{ route('tenant.shifts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.shifts.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Shifts & Roster
            </a>
            <a href="{{ route('tenant.timesheets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.timesheets.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Timesheets
            </a>
            <a href="{{ route('tenant.overtime.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.overtime.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('app.overtime') }}
            </a>
            @endif

            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_payroll') || auth()->user()->hasPermission('view_payroll'))
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Finance</p>
            <a href="{{ route('tenant.loans.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.loans.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Loans & Advances
            </a>
            <a href="{{ route('tenant.expenses.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.expenses.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Expense Claims
            </a>
            <a href="{{ route('tenant.statutory.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.statutory.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Statutory Returns
            </a>
            <a href="{{ route('tenant.bank-files.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.bank-files.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Bank Files
            </a>
            @endif

            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Offboarding</p>
            <a href="{{ route('tenant.separations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.separations.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Separation & Offboarding
            </a>
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Compliance</p>
            <a href="{{ route('tenant.licenses.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.licenses.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Licenses
            </a>
            <a href="{{ route('tenant.training.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.training.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Training & CPD
            </a>
            <a href="{{ route('tenant.disciplinary.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.disciplinary.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Disciplinary
            </a>
            <a href="{{ route('tenant.grievances.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.grievances.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Grievances
            </a>

            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_branches'))
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Branches</p>
            <a href="{{ route('tenant.branches.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branches.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                {{ __('app.branches') }}
            </a>
            @endif

            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Documents & Assets</p>
            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_documents'))
            <a href="{{ route('tenant.documents.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.documents.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('app.documents') }}
            </a>
            @endif
            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_assets'))
            <a href="{{ route('tenant.assets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.assets.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                {{ __('app.assets') }}
            </a>
            @endif
            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_contracts'))
            <a href="{{ route('tenant.contracts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.contracts.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ __('app.contracts') }}
            </a>
            @endif

            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Admin</p>
            @if(auth()->user()->is_admin)
            <a href="{{ route('tenant.roles.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.roles.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Roles & Permissions
            </a>
            @endif
            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_users'))
            <a href="{{ route('tenant.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.users.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Users
            </a>
            @endif
            @if(auth()->user()->is_admin)
            <a href="{{ route('tenant.audit.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.audit.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Audit Logs
            </a>
            <a href="{{ route('tenant.subscription.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.subscription.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Subscription
            </a>
            <a href="{{ route('tenant.wallet.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.wallet.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Wallet
            </a>
            @endif
            @if(auth()->user()->is_admin || auth()->user()->hasPermission('manage_announcements'))
            <a href="{{ route('tenant.announcements.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.announcements.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                {{ __('app.announcements') }}
            </a>
            @endif
            <a href="{{ route('tenant.support.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.support.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                {{ __('app.support') }}
            </a>
            <a href="{{ route('tenant.ip-whitelist.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.ip-whitelist.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                IP Whitelist
            <a href="{{ route('tenant.settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.settings.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>


        <div class="px-4 py-4 border-t border-white/10 space-y-2">
            @if(auth()->user()->canSwitchPortal())
            <form method="POST" action="{{ route('tenant.portal.switch') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Employee Portal
                </button>
            </form>
            @endif
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-medium">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white/80 text-xs truncate">{{ auth()->user()->name }}</div>
                    <div class="text-white/40 text-xs">{{ auth()->user()->is_admin ? 'Administrator' : (auth()->user()->tenantRoles()->first()?->role?->name ?? 'Staff') }}</div>
                </div>
                <form method="POST" action="{{ route('tenant.logout') }}">
                    @csrf
                    <button type="submit" class="text-white/40 hover:text-white/80">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-100 px-8 h-14 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-base font-medium text-emerald-900">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-gray-400">@yield('page-subtitle', '')</p>
            </div>
            <div class="flex items-center gap-4">
                <form method="GET" action="{{ route('tenant.search') }}" class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..."
                           class="w-48 pl-8 pr-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:w-64 transition-all duration-200"/>
                    <svg class="w-4 h-4 text-gray-400 absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </form>
                @php
                    try {
                        $unreadCount = \App\Models\Tenant\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                    } catch (\Exception $e) {
                        $unreadCount = 0;
                    }
                @endphp
                <a href="{{ route('tenant.notifications.index') }}" class="relative text-gray-400 hover:text-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </a>
                <div class="flex items-center gap-1 border border-gray-200 rounded-lg overflow-hidden">
                    <form method="POST" action="{{ route('tenant.lang.switch') }}">
                        @csrf
                        <input type="hidden" name="locale" value="en"/>
                        <button type="submit" class="px-2 py-1 text-xs font-medium {{ session('locale', 'en') === 'en' ? 'bg-emerald-700 text-white' : 'text-gray-500 hover:bg-gray-50' }}">EN</button>
                    </form>
                    <form method="POST" action="{{ route('tenant.lang.switch') }}">
                        @csrf
                        <input type="hidden" name="locale" value="sw"/>
                        <button type="submit" class="px-2 py-1 text-xs font-medium {{ session('locale') === 'sw' ? 'bg-emerald-700 text-white' : 'text-gray-500 hover:bg-gray-50' }}">SW</button>
                    </form>
                </div>
                @yield('page-actions')
            </div>
        </header>
        <main class="flex-1 overflow-y-auto p-8 bg-green-50/30">
            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mb-4 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">&times;</button>
            </div>
            @endif
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mb-4 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg px-4 py-3">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">&times;</button>
            </div>
            @endif
            @yield('content')
        </main>
    </div>

</div>
@livewireScripts
</body>
</html>











































