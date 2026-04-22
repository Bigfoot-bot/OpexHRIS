<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — HRIS Portal</title>
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

            @if(session('success'))
                {{-- Success State --}}
                <div class="text-center">
                    <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-medium text-emerald-900 mb-2">Check Your Email</h2>
                    <p class="text-sm text-gray-500 mb-6">{{ session('success') }}</p>
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <p class="text-xs text-gray-500 mb-1">Didnt receive the link?</p>
                        <p class="text-xs text-gray-400">You can resend in <span id="countdown" class="font-medium text-emerald-700">60</span> seconds</p>
                        <form method="POST" action="{{ route('tenant.password.email') }}" id="resend-form" class="mt-3">
                            @csrf
                            <input type="hidden" name="email" value="{{ old('email') }}"/>
                            <button type="submit" id="resend-btn" disabled
                                    class="w-full bg-gray-200 text-gray-400 text-sm font-medium py-2 rounded-lg transition-colors cursor-not-allowed"
                                    id="resend-btn">
                                Resend Link
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('tenant.login') }}" class="text-sm text-emerald-600 hover:text-emerald-800">
                        Back to Login
                    </a>
                </div>
                <script>
                    let seconds = 60;
                    const countdown = document.getElementById('countdown');
                    const resendBtn = document.getElementById('resend-btn');
                    const timer = setInterval(function() {
                        seconds--;
                        countdown.textContent = seconds;
                        if (seconds <= 0) {
                            clearInterval(timer);
                            resendBtn.disabled = false;
                            resendBtn.className = 'w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg transition-colors cursor-pointer';
                            countdown.parentElement.textContent = 'You can now resend the link.';
                        }
                    }, 1000);
                </script>
            @else
                {{-- Form State --}}
                <div class="text-center mb-6">
                    <h2 class="text-lg font-medium text-emerald-900">Forgot Password?</h2>
                    <p class="text-sm text-gray-500 mt-1">Enter your email and we will send you a reset link</p>
                </div>
                @if(session('error'))
                    <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('tenant.password.email') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                   placeholder="your@email.com"/>
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2.5 rounded-lg text-sm mt-6 transition-colors">
                        Send Reset Link
                    </button>
                </form>
                <p class="text-center text-xs text-gray-400 mt-4">
                    Remember your password?
                    <a href="{{ route('tenant.login') }}" class="text-emerald-600 hover:text-emerald-800">Sign in</a>
                </p>
            @endif

        </div>
    </div>
</body>
</html>
