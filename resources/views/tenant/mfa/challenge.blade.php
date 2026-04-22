<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MFA Verification - {{ tenant('name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h1 class="text-xl font-semibold text-gray-900">Two-Factor Authentication</h1>
                <p class="text-sm text-gray-500 mt-1">Enter the verification code sent to your email</p>
            </div>

            @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>@endif

            <form method="POST" action="{{ route('tenant.mfa.verify') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Verification Code</label>
                    <input type="text" name="code" maxlength="6" required autofocus
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 text-center text-2xl font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="000000"/>
                </div>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2.5 rounded-lg text-sm">Verify</button>
            </form>

            <div class="mt-4 text-center">
                <form method="POST" action="{{ route('tenant.mfa.send-code') }}">
                    @csrf
                    <button type="submit" class="text-sm text-emerald-600 hover:text-emerald-800">Send new code</button>
                </form>
            </div>

            <div class="mt-4 text-center">
                <form method="POST" action="{{ route('tenant.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-gray-600">Sign out</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
