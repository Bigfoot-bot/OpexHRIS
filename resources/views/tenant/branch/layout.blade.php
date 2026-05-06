<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $branch->name ?? 'Branch' }} - {{ tenant('name') }}</title>
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
                    <div class="text-white text-sm font-medium truncate">{{ $branch->name ?? 'Branch' }}</div>
                    <div class="text-white/40 text-xs">{{ tenant('name') }}</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('tenant.branch.dashboard', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.dashboard') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Employees</p>
            <a href="{{ route('tenant.branch.employees', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.employees*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Employees
            </a>

            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Leave</p>
            <a href="{{ route('tenant.branch.leave', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.leave*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Leave Requests
            </a>

            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Documents & Assets</p>
            <a href="{{ route('tenant.branch.documents', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.documents*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Documents
            </a>
            <a href="{{ route('tenant.branch.assets', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.assets*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                Assets
            </a>
            <a href="{{ route('tenant.branch.contracts', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.contracts*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Contracts
            </a>

            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Payroll & Finance</p>
            <a href="{{ route('tenant.branch.payroll', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.payroll*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Payroll
            </a>
            <a href="{{ route('tenant.branch.loans.index', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.loans*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Loans
            </a>
            <a href="{{ route('tenant.branch.reports', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.reports') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Reports
            </a>
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Communication</p>
            <a href="{{ route('tenant.branch.announcements', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.announcements*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Announcements
            </a>
            <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Settings</p>
            <a href="{{ route('tenant.branch.settings', $branch) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.branch.settings') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-white/10 space-y-2">
            @if(auth()->user()->is_admin)
            <a href="{{ route('tenant.dashboard') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Back to Main Portal
            </a>
            @elseif(auth()->user()->employee_id)
            <a href="{{ route('tenant.employee.dashboard') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Switch to Employee Portal
            </a>
            @endif
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-medium">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white/80 text-xs truncate">{{ auth()->user()->name }}</div>
                    <div class="text-white/40 text-xs">
                        @if(auth()->user()->is_admin)
                            Administrator
                        @elseif(auth()->user()->hasRole('Branch Manager'))
                            Branch Manager
                        @elseif(auth()->user()->hasRole('Branch HR'))
                            Branch HR
                        @else
                            {{ auth()->user()->tenantRoles()->first()?->role?->name ?? 'Staff' }}
                        @endif
                    </div>
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
                <p class="text-xs text-gray-400">@yield('page-subtitle', $branch->name ?? '')</p>
            </div>
            <div class="flex items-center gap-4">
                @yield('page-actions')
            </div>
        </header>
        <main class="flex-1 overflow-y-auto p-8 bg-green-50/30">
            @yield('content')
        </main>
    </div>

</div>
@livewireScripts
</body>
</html>





