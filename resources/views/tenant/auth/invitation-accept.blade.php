<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation — HRIS Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-emerald-50 to-green-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-emerald-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <div class="w-7 h-7 bg-white rounded-lg"></div>
            </div>
            <h1 class="text-xl font-medium text-emerald-900">OpEx HRIS</h1>
            <p class="text-sm text-gray-500 mt-1">Healthcare HR Management Platform</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-8">
            <div class="text-center mb-6">
                <h2 class="text-lg font-medium text-emerald-900">Activate Your Account</h2>
                <p class="text-sm text-gray-500 mt-1">You have been invited as <span class="font-medium text-emerald-700">{{ $invitation->role }}</span></p>
            </div>
            @if($errors->any())
                <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('tenant.invitation.store', $invitation->token) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Email Address *</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Enter your email address"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Create Password *</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Minimum 8 characters"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Repeat your password"/>
                    </div>
                </div>
                <button type="submit"
                        class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2.5 rounded-lg text-sm mt-6 transition-colors">
                    Activate My Account
                </button>
            </form>
            <p class="text-center text-xs text-gray-400 mt-4">
                Already have an account?
                <a href="{{ route('tenant.login') }}" class="text-emerald-600 hover:text-emerald-800">Sign in</a>
            </p>
        </div>
        <p class="text-center text-xs text-gray-400 mt-6">
            This invitation expires {{ $invitation->expires_at->format('M d, Y') }}
        </p>
    </div>
</body>
</html>
