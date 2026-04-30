<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($branding) ? $branding->platform_name : 'OpEx HRIS' }} - Super Admin</title>
    @if(isset($branding) && $branding->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('branding/' . $branding->favicon) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <aside class="w-56 flex flex-col flex-shrink-0" style="background-color: {{ isset($branding) ? $branding->primary_color : '#064e3b' }};">  
            <div class="px-5 py-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    @if(isset($branding) && $branding->logo)
                        <img src="{{ asset('branding/' . $branding->logo) }}" alt="Logo" class="w-8 h-8 object-contain rounded-lg bg-white p-0.5"/>
                    @else
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                            <div class="w-4 h-4 bg-emerald-600 rounded-sm"></div>
                        </div>
                    @endif
                    <div>
                        <div class="text-white text-sm font-medium">{{ isset($branding) ? $branding->platform_name : 'OpEx HRIS' }}</div>
                        <div class="text-white/40 text-xs">Super Admin</div>
                    </div>
                </div>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                <p class="text-white/30 text-xs px-2 pb-2 uppercase tracking-widest">Main</p>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.tenants.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.tenants.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Facilities
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.invoices.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    Invoices
                </a>
                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">System</p>
                <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.audit-logs.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Audit Logs
                </a>
                <a href="{{ route('admin.support.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.support.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Support Tickets
                </a>
                <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.announcements.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    Announcements
                </a>
                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Finance</p>
                <a href="{{ route('admin.wallets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.wallets.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Wallets
                </a>
                <a href="{{ route('admin.daraja-settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.daraja-settings.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    M-Pesa Settings
                </a>
                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Subscriptions</p>
                <a href="{{ route('admin.subscription-plans.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.subscription-plans.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Subscription Plans
                </a>
                <a href="{{ route('admin.subscription-settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.subscription-settings.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Subscription Settings
                </a>
                <a href="{{ route('admin.subscription-payments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.subscription-payments.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    Subscription Payments
                </a>
                <p class="text-white/30 text-xs px-2 pt-3 pb-2 uppercase tracking-widest">Settings</p>
                <a href="{{ route('admin.mail-settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.mail-settings.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Mail Settings
                </a>
                <a href="{{ route('admin.branding-settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.branding-settings.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Branding
                </a>
                <a href="{{ route('admin.integrations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.integrations.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                    Integrations
                </a>
                <a href="{{ route('admin.mfa.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.mfa.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    MFA Settings
                </a>
                <a href="{{ route('admin.general-settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.general-settings.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    General Settings
                </a>
                <a href="{{ route('admin.statutory-deductions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.statutory-deductions.*') ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/10 hover:text-white/90' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Statutory Deductions
                </a>
            </nav>
            <div class="px-4 py-4 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-medium">
                        {{ strtoupper(substr(auth('super_admin')->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white/80 text-xs truncate">{{ auth('super_admin')->user()->name }}</div>
                        <div class="text-white/40 text-xs">Super Admin</div>
                    </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-white/40 hover:text-white/80 cursor-pointer">
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
                <div class="flex items-center gap-3">
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




