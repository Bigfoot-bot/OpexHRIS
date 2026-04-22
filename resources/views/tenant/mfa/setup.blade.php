@extends('tenant.layouts.app')
@section('page-title', 'Two-Factor Authentication')
@section('page-subtitle', 'Manage your MFA settings')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>@endif

        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Two-Factor Authentication</h2>
                <p class="text-xs text-gray-400">Add an extra layer of security to your account</p>
            </div>
            <div class="ml-auto">
                <span class="text-xs px-3 py-1 rounded-full {{ auth()->user()->mfa_enabled ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ auth()->user()->mfa_enabled ? 'Enabled' : 'Disabled' }}
                </span>
            </div>
        </div>

        <div class="space-y-4 text-sm text-gray-600 mb-6">
            <p>When MFA is enabled, you will be required to enter a 6-digit verification code sent to your email address each time you log in.</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Protects against unauthorized access</li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Code sent to {{ auth()->user()->email }}</li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Code expires after 10 minutes</li>
            </ul>
        </div>

        @if(!auth()->user()->mfa_enabled)
            <form method="POST" action="{{ route('tenant.mfa.enable') }}">
                @csrf
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2.5 rounded-lg">Enable MFA</button>
            </form>
        @else
            <form method="POST" action="{{ route('tenant.mfa.disable') }}">
                @csrf
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2.5 rounded-lg">Disable MFA</button>
            </form>
        @endif
    </div>
</div>
@endsection
