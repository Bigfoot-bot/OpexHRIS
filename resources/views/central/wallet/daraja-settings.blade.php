@extends('central.layouts.app')

@section('page-title', 'Daraja M-Pesa Settings')
@section('page-subtitle', 'Configure M-Pesa Daraja API credentials')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">M-Pesa Daraja API</h2>
                <p class="text-xs text-gray-400">Safaricom Daraja API credentials for STK Push</p>
            </div>
            @if($settings->is_active)
                <span class="ml-auto text-xs bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 rounded-full">Active</span>
            @else
                <span class="ml-auto text-xs bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full">Inactive</span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.daraja-settings.update') }}">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Consumer Key *</label>
                        <input type="text" name="consumer_key" value="{{ old('consumer_key', $settings->consumer_key) }}" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Your Daraja consumer key"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Consumer Secret *</label>
                        <input type="password" name="consumer_secret" value="{{ old('consumer_secret', $settings->consumer_secret) }}" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Your Daraja consumer secret"/>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Paybill Number *</label>
                        <input type="text" name="paybill_number" value="{{ old('paybill_number', $settings->paybill_number) }}" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="e.g. 174379"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Passkey *</label>
                        <input type="password" name="passkey" value="{{ old('passkey', $settings->passkey) }}" required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Your Daraja passkey"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Callback URL *</label>
                    <input type="url" name="callback_url" value="{{ old('callback_url', $settings->callback_url) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="https://yourdomain.com/daraja/callback"/>
                    <p class="text-xs text-gray-400 mt-1">Must be a publicly accessible HTTPS URL</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Environment *</label>
                        <select name="environment" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="sandbox" {{ $settings->environment === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                            <option value="production" {{ $settings->environment === 'production' ? 'selected' : '' }}>Production (Live)</option>
                        </select>
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="is_active" value="1" {{ $settings->is_active ? 'checked' : '' }}
                                   class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"/>
                            <span class="text-sm text-gray-600">Enable Daraja M-Pesa</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Save Settings
                </button>
                <a href="{{ route('admin.daraja-settings.test') }}"
                   onclick="event.preventDefault(); document.getElementById('test-form').submit();"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Test Connection
                </a>
            </div>
        </form>
        <form id="test-form" method="POST" action="{{ route('admin.daraja-settings.test') }}" class="hidden">@csrf</form>
    </div>

    {{-- Paybill Info --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-blue-800 mb-2">?? How facilities pay via M-Pesa</h3>
        <div class="space-y-1 text-xs text-blue-700">
            <p>1. Go to M-Pesa ? Lipa na M-Pesa ? Pay Bill</p>
            <p>2. Business No: <strong>{{ $settings->paybill_number ?? 'Not configured' }}</strong></p>
            <p>3. Account No: <strong>Their facility slug (e.g. nairobi-west-hospital)</strong></p>
            <p>4. Enter amount and PIN</p>
            <p>5. Submit transaction code in HRIS portal</p>
        </div>
    </div>

</div>
@endsection
