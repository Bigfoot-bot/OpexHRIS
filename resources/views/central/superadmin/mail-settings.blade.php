@extends('central.layouts.app')

@section('title', 'Mail Settings')

@section('content')
<div class="p-6 max-w-2xl mx-auto">

    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-800">Mail Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Configure Gmail SMTP for sending emails across the platform</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    {{-- SMTP Settings --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Gmail SMTP Configuration</h2>
                <p class="text-xs text-gray-400">Use a Gmail account with an App Password</p>
            </div>
            @if($settings->is_configured)
                <span class="ml-auto text-xs bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 rounded-full">Configured</span>
            @else
                <span class="ml-auto text-xs bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full">Not Configured</span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.mail-settings.update') }}">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Gmail Address *</label>
                    <input type="email" name="mail_username" value="{{ old('mail_username', $settings->mail_username) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="yourmail@gmail.com"/>
                    @error('mail_username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">App Password *</label>
                    <input type="password" name="mail_password" value="{{ old('mail_password', $settings->mail_password) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="16-character app password"/>
                    @error('mail_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Generate from Google Account &rarr; Security &rarr; App Passwords</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">From Name *</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings->mail_from_name) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="OpEx HRIS"/>
                    @error('mail_from_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">From Email Address *</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings->mail_from_address) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="noreply@yourmail.com"/>
                    @error('mail_from_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <button type="submit"
                    class="mt-6 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                Save Settings
            </button>
        </form>
    </div>

    {{-- Test Email --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Send Test Email</h2>
                <p class="text-xs text-gray-400">Verify your mail settings are working correctly</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.mail-settings.test') }}">
            @csrf
            <div class="flex gap-3">
                <input type="email" name="test_email" required
                       class="flex-1 px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="test@example.com"/>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors {{ !$settings->is_configured ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ !$settings->is_configured ? 'disabled' : '' }}>
                    Send Test
                </button>
            </div>
            @error('test_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </form>
    </div>

</div>
@endsection
