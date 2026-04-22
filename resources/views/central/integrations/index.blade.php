@extends('central.layouts.app')
@section('page-title', 'Integrations & API Settings')
@section('page-subtitle', 'Configure SMS, payment, and third-party integrations')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>@endif

    <form method="POST" action="{{ route('admin.integrations.update') }}">
        @csrf

        {{-- SMS Settings --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-1">SMS Notifications</h2>
            <p class="text-xs text-gray-400 mb-4">Configure AfricasTalking or Twilio for SMS alerts</p>
            <div class="grid grid-cols-2 gap-4">
                @foreach($settings->get('sms', collect()) as $setting)
                <div class="{{ $setting->type === 'toggle' ? 'col-span-2 flex items-center gap-3' : '' }}">
                    @if($setting->type === 'toggle')
                        <input type="hidden" name="settings[{{ $setting->key }}]" value="0"/>
                        <input type="checkbox" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}" value="1" {{ $setting->value === '1' ? 'checked' : '' }} class="rounded"/>
                        <label for="{{ $setting->key }}" class="text-sm text-gray-700">{{ $setting->label }}</label>
                    @elseif($setting->type === 'select' && $setting->key === 'sms_provider')
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $setting->label }}</label>
                        <select name="settings[{{ $setting->key }}]" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="africastalking" {{ $setting->value === 'africastalking' ? 'selected' : '' }}>AfricasTalking</option>
                            <option value="twilio" {{ $setting->value === 'twilio' ? 'selected' : '' }}>Twilio</option>
                        </select>
                    @else
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $setting->label }}</label>
                        <input type="{{ $setting->type === 'password' ? 'password' : 'text' }}"
                               name="settings[{{ $setting->key }}]"
                               value="{{ $setting->type === 'password' ? '' : $setting->value }}"
                               placeholder="{{ $setting->type === 'password' ? '(unchanged)' : '' }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    @endif
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-600 mb-2">Test SMS</p>
                <div class="flex gap-3">
                    <input type="text" id="test_phone" placeholder="+254712345678" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 w-48"/>
                    <button type="button" onclick="testSms()" style="background-color:#1d4ed8;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Send Test</button>
                </div>
            </div>
        </div>

        {{-- Payment Settings --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-1">M-Pesa / Payment Settings</h2>
            <p class="text-xs text-gray-400 mb-4">Configure M-Pesa Daraja API credentials</p>
            <div class="grid grid-cols-2 gap-4">
                @foreach($settings->get('payment', collect()) as $setting)
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $setting->label }}</label>
                    @if($setting->key === 'mpesa_environment')
                        <select name="settings[{{ $setting->key }}]" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="sandbox" {{ $setting->value === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                            <option value="production" {{ $setting->value === 'production' ? 'selected' : '' }}>Production</option>
                        </select>
                    @else
                        <input type="{{ $setting->type === 'password' ? 'password' : 'text' }}"
                               name="settings[{{ $setting->key }}]"
                               value="{{ $setting->type === 'password' ? '' : $setting->value }}"
                               placeholder="{{ $setting->type === 'password' ? '(unchanged)' : '' }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Integrations --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-1">Third-Party Integrations</h2>
            <p class="text-xs text-gray-400 mb-4">Job boards, accounting software, biometric devices, and calendar integrations</p>
            @php
                $groups = [
                    'Job Boards' => ['linkedin_client_id', 'linkedin_client_secret', 'brightermonday_api_key'],
                    'Accounting Software' => ['quickbooks_client_id', 'quickbooks_client_secret', 'xero_client_id', 'xero_client_secret'],
                    'Biometric Devices' => ['biometric_provider', 'biometric_ip', 'biometric_port', 'biometric_enabled'],
                    'Calendar Integration' => ['google_client_id', 'google_client_secret', 'outlook_client_id', 'outlook_client_secret'],
                ];
                $integrationSettings = $settings->get('integrations', collect())->keyBy('key');
            @endphp
            @foreach($groups as $groupName => $keys)
            <div class="mb-6">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ $groupName }}</p>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($keys as $key)
                    @if($setting = $integrationSettings->get($key))
                    <div class="{{ $setting->type === 'toggle' ? 'col-span-2 flex items-center gap-3' : '' }}">
                        @if($setting->type === 'toggle')
                            <input type="hidden" name="settings[{{ $setting->key }}]" value="0"/>
                            <input type="checkbox" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}" value="1" {{ $setting->value === '1' ? 'checked' : '' }} class="rounded"/>
                            <label for="{{ $setting->key }}" class="text-sm text-gray-700">{{ $setting->label }}</label>
                        @elseif($setting->key === 'biometric_provider')
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $setting->label }}</label>
                            <select name="settings[{{ $setting->key }}]" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="zkteco" {{ $setting->value === 'zkteco' ? 'selected' : '' }}>ZKTeco</option>
                                <option value="suprema" {{ $setting->value === 'suprema' ? 'selected' : '' }}>Suprema</option>
                                <option value="hikvision" {{ $setting->value === 'hikvision' ? 'selected' : '' }}>HikVision</option>
                            </select>
                        @else
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $setting->label }}</label>
                            <input type="{{ $setting->type === 'password' ? 'password' : 'text' }}"
                                   name="settings[{{ $setting->key }}]"
                                   value="{{ $setting->type === 'password' ? '' : $setting->value }}"
                                   placeholder="{{ $setting->type === 'password' ? '(unchanged)' : '' }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        @endif
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-8 py-2.5 rounded-lg">Save All Settings</button>
        </div>
    </form>
</div>

<script>
function testSms() {
    const phone = document.getElementById('test_phone').value;
    if (!phone) { alert('Please enter a phone number'); return; }
    fetch('{{ route('admin.integrations.test-sms') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ phone })
    }).then(() => alert('Test SMS sent!')).catch(() => alert('Error sending test SMS'));
}
</script>
@endsection
