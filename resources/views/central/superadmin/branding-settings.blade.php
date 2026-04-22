@extends('central.layouts.app')

@section('page-title', 'Branding Settings')
@section('page-subtitle', 'Customize the platform name, logo and favicon')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.branding-settings.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Platform Identity --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5">Platform Identity</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Platform Name *</label>
                    <input type="text" name="platform_name" value="{{ old('platform_name', $settings->platform_name) }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="e.g. OpEx HRIS"/>
                    <p class="text-xs text-gray-400 mt-1">Shown in the sidebar and email notifications</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Tagline</label>
                    <input type="text" name="platform_tagline" value="{{ old('platform_tagline', $settings->platform_tagline) }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="e.g. Healthcare HR Management Platform"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Primary Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color) }}"
                               class="w-12 h-10 rounded-lg border border-gray-200 cursor-pointer"/>
                        <input type="text" id="color_hex" value="{{ old('primary_color', $settings->primary_color) }}"
                               class="w-32 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="#064e3b"/>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Used for sidebar and buttons (requires page reload to apply)</p>
                </div>
            </div>
        </div>

        {{-- Logo --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5">Logo</h2>
            <div class="flex items-start gap-6">
                <div class="w-24 h-24 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-center overflow-hidden">
                    @if($settings->logo)
                        <img src="{{ asset('branding/' . $settings->logo) }}" alt="Logo" class="w-full h-full object-contain p-2"/>
                    @else
                        <span class="text-xs text-gray-400">No logo</span>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Upload Logo</label>
                    <input type="file" name="logo" accept="image/png,image/jpg,image/jpeg,image/svg+xml"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"/>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG or SVG. Max 2MB. Recommended: 200x200px</p>
                </div>
            </div>
        </div>

        {{-- Favicon --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5">Favicon</h2>
            <div class="flex items-start gap-6">
                <div class="w-16 h-16 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-center overflow-hidden">
                    @if($settings->favicon)
                        <img src="{{ asset('branding/' . $settings->favicon) }}" alt="Favicon" class="w-full h-full object-contain p-2"/>
                    @else
                        <span class="text-xs text-gray-400">None</span>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Upload Favicon</label>
                    <input type="file" name="favicon" accept="image/png,image/jpg,image/x-icon"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"/>
                    <p class="text-xs text-gray-400 mt-1">PNG or ICO. Max 512KB. Recommended: 32x32px</p>
                </div>
            </div>
        </div>

        {{-- Bank & Payment Details --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Bank & Payment Details</h2>
            <p class="text-xs text-gray-400 mb-4">These details appear on invoices and payment instructions shown to facilities.</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ $settings->bank_name ?? '' }}" placeholder="e.g. KCB Bank Kenya" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Account Name</label>
                    <input type="text" name="bank_account_name" value="{{ $settings->bank_account_name ?? '' }}" placeholder="e.g. OpEx Healthcare Consultancy Ltd" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Account Number</label>
                    <input type="text" name="bank_account_number" value="{{ $settings->bank_account_number ?? '' }}" placeholder="e.g. 1234567890" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Branch</label>
                    <input type="text" name="bank_branch" value="{{ $settings->bank_branch ?? '' }}" placeholder="e.g. Nairobi Main" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">M-Pesa Paybill Number</label>
                    <input type="text" name="paybill_number" value="{{ $settings->paybill_number ?? '' }}" placeholder="e.g. 174379" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">M-Pesa Account No <span class="text-gray-400 font-normal">(shown to payer)</span></label>
                    <input type="text" name="mpesa_account" value="{{ $settings->mpesa_account ?? '' }}" placeholder="e.g. ACCOUNT123 or nairobi-west" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
            </div>
        </div>

        <button type="submit"
                class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-3 rounded-xl transition-colors">
            Save Branding Settings
        </button>
    </form>
</div>

<script>
    // Sync color picker with hex input
    const colorInput = document.querySelector('input[type="color"]');
    const hexInput = document.getElementById('color_hex');
    colorInput.addEventListener('input', () => hexInput.value = colorInput.value);
    hexInput.addEventListener('input', () => colorInput.value = hexInput.value);
</script>
@endsection


