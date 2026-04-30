<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant('name') ?? 'HRIS' }} - Set Your Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="flex justify-center mb-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-700 rounded-xl flex items-center justify-center">
                    <div class="w-5 h-5 bg-white rounded-sm"></div>
                </div>
                <div>
                    <div class="text-emerald-900 text-lg font-medium">{{ tenant('name') ?? 'HRIS Platform' }}</div>
                    <div class="text-emerald-600 text-xs">Employee Portal</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-green-100 p-8 shadow-sm">
            <h2 class="text-xl font-medium text-emerald-900 mb-1">Set Your Password</h2>
            <p class="text-sm text-gray-400 mb-6">Enter the temporary password from your welcome email, then choose a new password.</p>

            @if($errors->any())
                <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.set-password.store') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 placeholder:text-gray-300"
                           placeholder="you@facility.com"/>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Current (temporary) password --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Temporary Password <span class="text-gray-400 font-normal">(from your welcome email)</span></label>
                    <div style="position:relative;">
                        <input type="password" name="current_password" id="current_password" required
                               class="w-full px-4 py-2.5 pr-10 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 placeholder:text-gray-300"
                               placeholder="Enter temporary password"/>
                        <button type="button" onclick="togglePassword('current_password')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
                            <svg id="eye-current_password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New password --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-2.5 pr-10 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 placeholder:text-gray-300"
                               placeholder="Min. 8 characters" oninput="checkMatch()"/>
                        <button type="button" onclick="togglePassword('password')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
                            <svg id="eye-password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm password --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Confirm New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-4 py-2.5 pr-10 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 placeholder:text-gray-300"
                               placeholder="Re-enter new password" oninput="checkMatch()"/>
                        <button type="button" onclick="togglePassword('password_confirmation')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
                            <svg id="eye-password_confirmation" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <p id="match-msg" class="text-xs mt-1" style="display:none;"></p>
                </div>

                <button type="submit" id="submit-btn"
                        class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2.5 rounded-lg transition-colors duration-150">
                    Set Password &amp; Continue
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Already have a password? <a href="{{ route('tenant.login') }}" class="text-emerald-600 hover:text-emerald-800">Sign in</a>
        </p>
    </div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const eye = document.getElementById('eye-' + id);
    if (input.type === 'password') {
        input.type = 'text';
        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>';
    } else {
        input.type = 'password';
        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

function checkMatch() {
    const pw  = document.getElementById('password').value;
    const cpw = document.getElementById('password_confirmation').value;
    const msg = document.getElementById('match-msg');
    const btn = document.getElementById('submit-btn');

    if (cpw.length === 0) {
        msg.style.display = 'none';
        btn.disabled = false;
        return;
    }

    if (pw === cpw) {
        msg.style.display = 'block';
        msg.style.color = '#059669';
        msg.textContent = 'Passwords match.';
        btn.disabled = false;
    } else {
        msg.style.display = 'block';
        msg.style.color = '#dc2626';
        msg.textContent = 'Passwords do not match.';
        btn.disabled = true;
    }
}
</script>
</body>
</html>
