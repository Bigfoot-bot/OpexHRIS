@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Checkout')
@section('page-subtitle', 'Complete your subscription payment')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('errors'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
    @endif

    {{-- Order Summary --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Order Summary</h2>
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">{{ $plan->name }} Plan ({{ $months }} month{{ $months > 1 ? 's' : '' }})</span>
                <span class="font-medium">KES {{ number_format($subtotal, 2) }}</span>
            </div>
            @if($discountAmt > 0)
            <div class="flex justify-between text-sm text-emerald-600">
                <span>Discount ({{ number_format($discountPct, 0) }}% off)</span>
                <span>- KES {{ number_format($discountAmt, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm text-gray-600">
                <span>VAT ({{ $settings->vat_percentage }}%)</span>
                <span>KES {{ number_format($vatAmt, 2) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold text-gray-800 border-t border-gray-100 pt-2 mt-2">
                <span>Total</span>
                <span class="text-emerald-700">KES {{ number_format($total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Payment Forms Side by Side --}}
    <div style="display:flex; gap:24px; align-items:stretch;">

        {{-- M-Pesa Manual --}}
        <div style="flex:1; background:white; border-radius:16px; border:1px solid #e5e7eb; padding:24px; display:flex; flex-direction:column;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                <div style="width:36px; height:36px; background:#fef3c7; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg style="width:20px; height:20px;" fill="none" stroke="#d97706" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:14px; font-weight:600; color:#111827;">M-Pesa Manual</p>
                    <p style="font-size:11px; color:#6b7280;">Pay to Paybill then submit code</p>
                </div>
            </div>
            <div style="background:#fffbeb; border-radius:10px; padding:12px; margin-bottom:16px; font-size:12px; color:#92400e;">
                <p><strong>Paybill:</strong> {{ $branding->paybill_number ?? \App\Models\Central\DarajaSetting::getSettings()->paybill_number ?? 'Contact support' }}</p>
                <p><strong>Account No:</strong> {{ $branding->mpesa_account ?? tenant()->slug ?? tenant('id') }}</p>
                <p><strong>Amount:</strong> KES {{ number_format($total, 2) }}</p>
            </div>
            <form method="POST" action="{{ route('tenant.subscription.pay') }}" style="display:flex; flex-direction:column; flex:1;">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}"/>
                <input type="hidden" name="cycle" value="{{ $cycle }}"/>
                <input type="hidden" name="payment_method" value="mpesa_manual"/>
                <input type="hidden" name="amount" value="{{ $total }}"/>
                <div style="flex:1;">
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11px; font-weight:500; color:#4b5563; margin-bottom:4px;">Transaction Code *</label>
                        <input type="text" name="transaction_reference" required
                               style="width:100%; padding:8px 12px; border:1px solid {{ $errors->has('transaction_reference') ? '#ef4444' : '#e5e7eb' }}; border-radius:8px; font-size:13px; box-sizing:border-box;"
                               placeholder="e.g. QHX7K2L9PW" value="{{ old('transaction_reference') }}"/>
                        @error('transaction_reference')
                            <p style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11px; font-weight:500; color:#4b5563; margin-bottom:4px;">Phone Number *</label>
                        <input type="text" name="mpesa_phone" required
                               style="width:100%; padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; box-sizing:border-box;"
                               placeholder="e.g. 0712345678" value="{{ old('mpesa_phone') }}"/>
                    </div>
                </div>
                <button type="submit"
                        style="width:100%; margin-top:16px; background:#d97706; color:white; border:none; padding:10px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                    Submit M-Pesa Payment
                </button>
            </form>
        </div>

        {{-- Bank Transfer --}}
        <div style="flex:1; background:white; border-radius:16px; border:1px solid #e5e7eb; padding:24px; display:flex; flex-direction:column;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                <div style="width:36px; height:36px; background:#eff6ff; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg style="width:20px; height:20px;" fill="none" stroke="#2563eb" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:14px; font-weight:600; color:#111827;">Bank Transfer</p>
                    <p style="font-size:11px; color:#6b7280;">Transfer to HRIS account & upload slip</p>
                </div>
            </div>
            <div style="background:#eff6ff; border-radius:10px; padding:12px; margin-bottom:16px; font-size:12px; color:#1e40af;">
                <p><strong>Bank:</strong> {{ $branding->bank_name ?? 'KCB Bank Kenya' }}</p>
                <p><strong>Account:</strong> {{ $branding->bank_account_name ?? 'OpEx Healthcare Consultancy Ltd' }}</p>
                <p><strong>Acc No:</strong> {{ $branding->bank_account_number ?? 'N/A' }}</p>
                <p><strong>Branch:</strong> {{ $branding->bank_branch ?? 'N/A' }}</p>
                <p><strong>Ref:</strong> {{ tenant()->slug ?? tenant('id') }}</p>
                <p><strong>Amount:</strong> KES {{ number_format($total, 2) }}</p>
            </div>
            <form method="POST" action="{{ route('tenant.subscription.pay') }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; flex:1;">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}"/>
                <input type="hidden" name="cycle" value="{{ $cycle }}"/>
                <input type="hidden" name="payment_method" value="bank_transfer"/>
                <input type="hidden" name="amount" value="{{ $total }}"/>
                <div style="flex:1;">
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11px; font-weight:500; color:#4b5563; margin-bottom:4px;">Your Bank Name *</label>
                        <input type="text" name="bank_name" required
                               style="width:100%; padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; box-sizing:border-box;"
                               placeholder="e.g. Equity Bank" value="{{ old('bank_name') }}"/>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11px; font-weight:500; color:#4b5563; margin-bottom:4px;">Transaction Reference *</label>
                        <input type="text" name="bank_reference" required
                               style="width:100%; padding:8px 12px; border:1px solid {{ $errors->has('bank_reference') ? '#ef4444' : '#e5e7eb' }}; border-radius:8px; font-size:13px; box-sizing:border-box;"
                               placeholder="Reference number" value="{{ old('bank_reference') }}"/>
                        @error('bank_reference')
                            <p style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:11px; font-weight:500; color:#4b5563; margin-bottom:4px;">Upload Slip (optional)</label>
                        <input type="file" name="proof_file" accept="image/*,.pdf" style="width:100%; font-size:12px;"/>
                    </div>
                </div>
                <button type="submit"
                        style="width:100%; margin-top:16px; background:#2563eb; color:white; border:none; padding:10px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                    Submit Bank Transfer
                </button>
            </form>
        </div>
    </div>

    {{-- M-Pesa STK Push --}}
    @if(\App\Models\Central\DarajaSetting::getSettings()->is_active)
    <div style="background:white; border-radius:16px; border:1px solid #d1fae5; padding:24px;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <div style="width:36px; height:36px; background:#f0fdf4; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                <svg style="width:20px; height:20px;" fill="none" stroke="#16a34a" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p style="font-size:14px; font-weight:600; color:#111827;">Pay via M-Pesa STK Push (Instant)</p>
                <p style="font-size:11px; color:#6b7280;">Get an M-Pesa prompt - subscription activated automatically</p>
            </div>
            <span style="margin-left:auto; font-size:11px; background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; padding:3px 10px; border-radius:20px;">Recommended</span>
        </div>
        <form method="POST" action="{{ route('tenant.subscription.stk-push') }}" style="display:flex; gap:16px; align-items:flex-end;">
            @csrf
            <input type="hidden" name="plan_id" value="{{ $plan->id }}"/>
            <input type="hidden" name="cycle" value="{{ $cycle }}"/>
            <div style="flex:1;">
                <label style="display:block; font-size:11px; font-weight:500; color:#4b5563; margin-bottom:4px;">Phone Number *</label>
                <input type="text" name="phone" required
                       style="width:100%; padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; box-sizing:border-box;"
                       placeholder="e.g. 0712345678"/>
            </div>
            <div style="flex:1;">
                <label style="display:block; font-size:11px; font-weight:500; color:#4b5563; margin-bottom:4px;">Amount: KES {{ number_format($total, 2) }}</label>
                <input type="hidden" name="amount" value="{{ $total }}"/>
                <input type="text" value="KES {{ number_format($total, 2) }}" disabled
                       style="width:100%; padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; box-sizing:border-box; background:#f9fafb;"/>
            </div>
            <div>
                <button type="submit"
                        style="background:#16a34a; color:white; border:none; padding:9px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; white-space:nowrap;">
                    Send STK Push
                </button>
            </div>
        </form>
    </div>
    @endif

    <a href="{{ route('tenant.subscription.plans') }}" class="block text-center text-sm text-gray-400 hover:text-gray-600">
        &larr; Back to Plans
    </a>
</div>
@endsection







