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
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

                <p class="text-white/30 text-xs px-2 pb-2 uppercase tracking-widest">Main</p>

                <a href="{{ route('tenant.employee.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                   {{ request()->routeIs('tenant.employee.dashboard') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Leave</p>

                <a href="{{ route('tenant.employee.leave') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                   {{ request()->routeIs('tenant.employee.leave') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    My Leave
                </a>

                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Payroll</p>

                <a href="{{ route('tenant.employee.payslips') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                   {{ request()->routeIs('tenant.employee.payslips') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    My Payslips
                </a>

                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Training</p>

                <a href="{{ route('tenant.employee.training') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                   {{ request()->routeIs('tenant.employee.training') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    My Training
                </a>

                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Performance</p>

                <a href="{{ route('tenant.employee.performance') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                   {{ request()->routeIs('tenant.employee.performance') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    My Performance
                </a>

                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">General</p>

                <a href="{{ route('tenant.employee.onboarding') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                   {{ request()->routeIs('tenant.employee.onboarding') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    My Onboarding
                </a>

                <a href="{{ route('tenant.announcements.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                   {{ request()->routeIs('tenant.announcements.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <span class="flex-1">Announcements</span>
                    @if(!empty($newAnnouncementCount) && $newAnnouncementCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5 min-w-[18px] text-center leading-none">
                            {{ $newAnnouncementCount > 9 ? '9+' : $newAnnouncementCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('tenant.employee.profile') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                   {{ request()->routeIs('tenant.employee.profile') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    My Profile
                </a>

                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Documents & Assets</p>
                <a href="{{ route('tenant.documents.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.documents.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Documents
                </a>
                <a href="{{ route('tenant.assets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.assets.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                    Assets
                </a>
                <a href="{{ route('tenant.contracts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('tenant.contracts.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Contracts
                </a>
            </nav>
            <div class="px-4 py-4 border-t border-white/10 space-y-2">
                @if(auth()->user()->hasAnyRole(['Branch Manager', 'Branch HR']) && auth()->user()->branch_id)
                <form method="POST" action="{{ route('tenant.branch.switch') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        {{ auth()->user()->roles->first()?->name ?? 'Branch' }} Portal
                    </button>
                </form>
                @endif
                @if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
                <form method="POST" action="{{ route('tenant.portal.switch') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-white/60 hover:bg-white/10 hover:text-white/90 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Admin Portal
                    </button>
                </form>
                @endif
                <div class="flex items-center gap-3">



                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-medium">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white/80 text-xs truncate">{{ auth()->user()->name }}</div>
                        <div class="text-white/40 text-xs">Employee</div>
                    </div>
                    <form method="POST" action="{{ route('tenant.logout') }}">
                        @csrf
                        <button type="submit" class="text-white/40 hover:text-white/80">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
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
                    @php
                        try {
                            $unreadCount = \App\Models\Tenant\Notification::where('user_id', auth()->id())
                                           ->where('is_read', false)->count();
                        } catch (\Exception $e) {
                            $unreadCount = 0;
                        }
                        try {
                            $employeeId = auth()->user()->employee_id;
                            $newAnnouncementCount = \App\Models\Announcement::where('tenant_id', tenant('id'))
                                ->where('type', 'facility')
                                ->where(function($q) use ($employeeId) {
                                    $q->whereNull('employee_id')->orWhere('employee_id', $employeeId);
                                })
                                ->where('created_at', '>=', now()->subDays(7))
                                ->count();
                        } catch (\Exception $e) {
                            $newAnnouncementCount = 0;
                        }
                    @endphp
                    <a href="{{ route('tenant.notifications.index') }}" class="relative text-gray-400 hover:text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </a>
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



