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

        {{-- Bulk SMS --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Bulk SMS to Customers</h2>
                    <p class="text-xs text-gray-400">Send an SMS broadcast to one or more active facilities</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.integrations.bulk-sms') }}">
                @csrf

                <div class="mb-4">
                    <label class="text-xs font-medium text-gray-600 block mb-1.5">Select Facilities</label>

                    {{-- Dropdown trigger --}}
                    <div class="relative" id="tenant-dropdown-wrap">
                        <button type="button" id="tenant-dropdown-btn"
                                onclick="toggleTenantDropdown()"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg border border-gray-200 text-sm text-left bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span id="tenant-dropdown-label" class="text-gray-400">Select facilities...</span>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Dropdown panel --}}
                        <div id="tenant-dropdown-panel"
                             style="display:none;position:absolute;z-index:50;width:100%;top:calc(100% + 4px);left:0;background:#fff;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 4px 16px rgba(0,0,0,.08);">

                            {{-- Search + actions --}}
                            <div style="padding:10px 12px;border-bottom:1px solid #f3f4f6;">
                                <input type="text" id="tenant-search" placeholder="Search facility..."
                                       oninput="filterTenants()"
                                       style="width:100%;padding:6px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;outline:none;"/>
                                <div style="display:flex;gap:12px;margin-top:8px;">
                                    <button type="button" onclick="selectAllTenants(true)"
                                            style="font-size:12px;color:#059669;background:none;border:none;cursor:pointer;padding:0;">Select All</button>
                                    <button type="button" onclick="selectAllTenants(false)"
                                            style="font-size:12px;color:#6b7280;background:none;border:none;cursor:pointer;padding:0;">Clear</button>
                                </div>
                            </div>

                            {{-- List --}}
                            <div id="tenant-list" style="max-height:220px;overflow-y:auto;">
                                @forelse($tenants as $tenant)
                                <label class="tenant-row" data-name="{{ strtolower($tenant->name) }}"
                                       style="display:flex;align-items:center;gap:12px;padding:10px 14px;cursor:pointer;border-bottom:1px solid #f9fafb;">
                                    <input type="checkbox" name="tenant_ids[]" value="{{ $tenant->id }}"
                                           class="tenant-checkbox"
                                           onchange="updateSelectedCount()"
                                           style="width:15px;height:15px;accent-color:#059669;flex-shrink:0;"/>
                                    <div style="flex:1;min-width:0;">
                                        <p style="font-size:13px;font-weight:500;color:#1f2937;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $tenant->name }}</p>
                                        @if($tenant->phone)
                                            <p style="font-size:11px;color:#9ca3af;margin:0;">{{ $tenant->phone }}</p>
                                        @else
                                            <p style="font-size:11px;color:#f59e0b;margin:0;">No phone on file</p>
                                        @endif
                                    </div>
                                </label>
                                @empty
                                <div style="padding:20px;text-align:center;font-size:13px;color:#9ca3af;">No active facilities found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <p id="selected-count" class="text-xs text-gray-400 mt-1">0 selected</p>
                    @error('tenant_ids')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-medium text-gray-600">Message</label>
                        <span id="sms-char-count" class="text-xs text-gray-400">0 / 160</span>
                    </div>
                    <textarea name="message" id="sms-message" rows="3" maxlength="160"
                              oninput="updateCharCount()"
                              class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                              placeholder="Type your SMS message here...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Max 160 characters (1 SMS unit).</p>
                </div>

                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Send Bulk SMS
                </button>
            </form>
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

function toggleTenantDropdown() {
    const panel = document.getElementById('tenant-dropdown-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('tenant-dropdown-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('tenant-dropdown-panel').style.display = 'none';
    }
});

function selectAllTenants(state) {
    document.querySelectorAll('.tenant-checkbox').forEach(cb => cb.checked = state);
    updateSelectedCount();
}

function filterTenants() {
    const q = document.getElementById('tenant-search').value.toLowerCase();
    document.querySelectorAll('.tenant-row').forEach(row => {
        row.style.display = row.dataset.name.includes(q) ? 'flex' : 'none';
    });
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.tenant-checkbox:checked');
    const n = checked.length;
    document.getElementById('selected-count').textContent = n + ' selected';

    const label = document.getElementById('tenant-dropdown-label');
    if (n === 0) {
        label.textContent = 'Select facilities...';
        label.style.color = '#9ca3af';
    } else {
        const names = Array.from(checked).map(cb => cb.closest('.tenant-row').querySelector('p').textContent.trim());
        label.textContent = n <= 2 ? names.join(', ') : names.slice(0, 2).join(', ') + ' +' + (n - 2) + ' more';
        label.style.color = '#1f2937';
    }
}

function updateCharCount() {
    const len = document.getElementById('sms-message').value.length;
    document.getElementById('sms-char-count').textContent = len + ' / 160';
}
</script>
@endsection
