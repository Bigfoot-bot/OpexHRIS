@extends('central.layouts.app')

@section('page-title', 'General Settings')
@section('page-subtitle', 'System-wide configuration and maintenance controls')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('password_success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">
            {{ session('password_success') }}
        </div>
    @endif

    @if($errors->any() && !session('password_error'))
        <div class="bg-red-50 border border-red-100 text-red-700 text-sm rounded-lg px-4 py-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <style>
        .toggle-wrap { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
        .toggle-wrap input { opacity: 0; width: 0; height: 0; position: absolute; }
        .toggle-slider {
            position: absolute; inset: 0; border-radius: 9999px;
            background: #e5e7eb; cursor: pointer; transition: background .2s;
        }
        .toggle-slider::before {
            content: ''; position: absolute;
            width: 18px; height: 18px; border-radius: 50%;
            background: #fff; left: 3px; top: 3px;
            box-shadow: 0 1px 3px rgba(0,0,0,.2);
            transition: transform .2s;
        }
        .toggle-wrap input:checked + .toggle-slider { background: #059669; }
        .toggle-wrap input:checked + .toggle-slider.amber { background: #f59e0b; }
        .toggle-wrap input:checked + .toggle-slider::before { transform: translateX(20px); }
        .toggle-wrap input:focus + .toggle-slider { outline: 2px solid #10b981; outline-offset: 2px; }
    </style>

    <form method="POST" action="{{ route('admin.general-settings.update') }}">
        @csrf

        {{-- Maintenance Mode --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Maintenance Mode</h2>
                    <p class="text-xs text-gray-400 mt-0.5">When enabled, all tenant users see the maintenance page. Super admins are unaffected.</p>
                </div>
                @if($settings->maintenance_mode)
                    <span class="text-xs bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full font-medium">Active</span>
                @else
                    <span class="text-xs bg-gray-50 text-gray-400 px-2.5 py-1 rounded-full">Off</span>
                @endif
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Enable Maintenance Mode</p>
                        <p class="text-xs text-gray-400 mt-0.5">Immediately blocks all tenant access</p>
                    </div>
                    <label class="toggle-wrap">
                        <input type="checkbox" name="maintenance_mode" value="1"
                               {{ old('maintenance_mode', $settings->maintenance_mode) ? 'checked' : '' }}>
                        <span class="toggle-slider amber"></span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Maintenance Message</label>
                    <textarea name="maintenance_message" rows="3"
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                              placeholder="Message shown to users during maintenance...">{{ old('maintenance_message', $settings->maintenance_message) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Displayed on the maintenance page visible to all tenant users.</p>
                </div>
            </div>
        </div>

        {{-- Registrations --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5">Registrations</h2>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div>
                    <p class="text-sm font-medium text-gray-700">Allow New Facility Registrations</p>
                    <p class="text-xs text-gray-400 mt-0.5">Disable to temporarily pause new tenant onboarding</p>
                </div>
                <label class="toggle-wrap">
                    <input type="checkbox" name="allow_new_registrations" value="1"
                           {{ old('allow_new_registrations', $settings->allow_new_registrations) ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        {{-- Support Contact --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5">Support Contact</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Support Email</label>
                    <input type="email" name="support_email"
                           value="{{ old('support_email', $settings->support_email) }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="e.g. support@opexhris.co.ke"/>
                    <p class="text-xs text-gray-400 mt-1">Shown in system emails and the tenant help section.</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Support Phone</label>
                    <input type="text" name="support_phone"
                           value="{{ old('support_phone', $settings->support_phone) }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="e.g. +254 700 000 000"/>
                </div>
            </div>
        </div>

        {{-- System --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5">System</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Default Timezone</label>
                    <select name="default_timezone"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach(\DateTimeZone::listIdentifiers(\DateTimeZone::AFRICA) as $tz)
                            <option value="{{ $tz }}" {{ old('default_timezone', $settings->default_timezone) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                        @foreach(\DateTimeZone::listIdentifiers(\DateTimeZone::UTC) as $tz)
                            <option value="{{ $tz }}" {{ old('default_timezone', $settings->default_timezone) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Used for scheduled tasks and date display across the platform.</p>
                </div>
            </div>
        </div>

        {{-- Security --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5">Security</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Max Login Attempts</label>
                    <input type="number" name="max_login_attempts" min="3" max="20"
                           value="{{ old('max_login_attempts', $settings->max_login_attempts) }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    <p class="text-xs text-gray-400 mt-1">Number of failed login attempts before a user is temporarily locked out.</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Session Lifetime (minutes)</label>
                    <input type="number" name="session_lifetime" min="15" max="1440"
                           value="{{ old('session_lifetime', $settings->session_lifetime) }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    <p class="text-xs text-gray-400 mt-1">How long an idle session remains active. Default is 120 minutes.</p>
                </div>
            </div>
        </div>

        <button type="submit"
                class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2.5 rounded-xl transition-colors">
            Save General Settings
        </button>

    </form>

    {{-- Change Password --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 bg-gray-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Change Password</h2>
                <p class="text-xs text-gray-400 mt-0.5">Update your super admin account password</p>
            </div>
        </div>

        @if(session('password_error') && $errors->any())
            <div class="bg-red-50 border border-red-100 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.general-settings.change-password') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Current Password</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password"
                               class="w-full px-4 py-2.5 pr-10 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('current_password') border-red-300 @enderror"
                               placeholder="Enter current password"/>
                        <button type="button" onclick="togglePw('current_password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">New Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="new_password"
                               oninput="checkPwMatch()"
                               class="w-full px-4 py-2.5 pr-10 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('password') border-red-300 @enderror"
                               placeholder="Minimum 8 characters"/>
                        <button type="button" onclick="togglePw('new_password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="confirm_password"
                               oninput="checkPwMatch()"
                               class="w-full px-4 py-2.5 pr-10 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Re-enter new password"/>
                        <button type="button" onclick="togglePw('confirm_password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <p id="pw-match-msg" class="text-xs mt-1 hidden"></p>
                </div>
            </div>

            <button type="submit" id="pw-submit-btn"
                    class="mt-5 text-sm font-medium rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                    style="background-color:#1f2937;color:#ffffff;padding:0.625rem 1.5rem;display:inline-block;">
                Change Password
            </button>
        </form>
    </div>

</div>

<script>
function togglePw(fieldId, btn) {
    const input = document.getElementById(fieldId);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.querySelector('.eye-icon').style.opacity = input.type === 'text' ? '0.4' : '1';
}

function checkPwMatch() {
    const np = document.getElementById('new_password').value;
    const cp = document.getElementById('confirm_password').value;
    const msg = document.getElementById('pw-match-msg');
    const btn = document.getElementById('pw-submit-btn');

    if (!cp) { msg.classList.add('hidden'); btn.disabled = false; return; }

    if (np === cp) {
        msg.textContent = 'Passwords match.';
        msg.className = 'text-xs mt-1 text-emerald-600';
        btn.disabled = false;
    } else {
        msg.textContent = 'Passwords do not match.';
        msg.className = 'text-xs mt-1 text-red-500';
        btn.disabled = true;
    }
}
</script>

@endsection
