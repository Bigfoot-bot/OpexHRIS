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
                <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-medium text-emerald-900">You're Invited!</h2>
                <p class="text-sm text-gray-500 mt-1">Set up your account to get started</p>
            </div>

            <div class="bg-emerald-50 rounded-lg p-4 mb-6">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">Name</p>
                    <p class="text-sm font-medium text-emerald-900">{{ $invitation->name }}</p>
                </div>
                <div class="space-y-1 mt-3">
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="text-sm text-emerald-900">{{ $invitation->email }}</p>
                </div>
                <div class="space-y-1 mt-3">
                    <p class="text-xs text-gray-500">Role</p>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">{{ $invitation->role }}</span>
                </div>
                <div class="space-y-1 mt-3">
                    <p class="text-xs text-gray-500">Expires</p>
                    <p class="text-xs text-amber-600">{{ $invitation->expires_at->format('M d, Y \a\t H:i') }}</p>
                </div>
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
                    Activate My Account →
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-4">
                Already have an account?
                <a href="{{ route('tenant.login') }}" class="text-emerald-600 hover:text-emerald-800">Sign in</a>
            </p>

        </div>

    </div>

</body>
</html>