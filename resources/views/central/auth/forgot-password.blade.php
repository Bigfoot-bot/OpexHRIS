<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — OpEx HRIS</title>
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
                <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-medium text-emerald-900">Forgot Password?</h2>
                <p class="text-sm text-gray-500 mt-1">Enter your email and we will send you a reset link</p>
            </div>
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">
                    {{ session('success') }}
                </div>
            @endif
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
            <form method="POST" action="{{ route('admin.password.email') }}" id="resetForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="admin@example.com"/>
                    </div>
                </div>
                <button type="submit" id="submitBtn"
                        class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2.5 rounded-lg text-sm mt-6 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Send Reset Link
                </button>
            </form>
            <p class="text-center text-xs text-gray-400 mt-4" id="countdownMsg" style="display:none;">
                You can request another link in <span id="countdown" class="font-medium text-emerald-600">60</span>s
            </p>
            <p class="text-center text-xs text-gray-400 mt-4">
                Remember your password?
                <a href="{{ route('admin.login') }}" class="text-emerald-600 hover:text-emerald-800">Sign in</a>
            </p>
        </div>
    </div>

    @if(session('success'))
    <script>
        const btn = document.getElementById('submitBtn');
        const msg = document.getElementById('countdownMsg');
        const countdownEl = document.getElementById('countdown');
        let seconds = 60;

        btn.disabled = true;
        msg.style.display = 'block';

        const timer = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                btn.disabled = false;
                msg.style.display = 'none';
            }
        }, 1000);
    </script>
    @endif
</body>
</html>
