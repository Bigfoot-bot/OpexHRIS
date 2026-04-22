@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', $invoice->status === 'paid' ? 'Receipt' : 'Invoice')
@section('page-subtitle', $invoice->status === 'paid' ? $invoice->receipt_number : $invoice->invoice_number)

@section('page-actions')
    <button onclick="window.print()" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
        Print / Download
    </button>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8" id="invoice-content">

        {{-- Header --}}
        <div class="flex justify-between items-start mb-8">
            <div>
                @if(isset($branding) && $branding->logo)
                    <img src="{{ asset('branding/' . $branding->logo) }}" alt="Logo" class="h-12 mb-2"/>
                @endif
                <h1 class="text-xl font-bold text-gray-800">{{ isset($branding) ? $branding->platform_name : 'OpEx HRIS' }}</h1>
                <p class="text-xs text-gray-400">Healthcare HR Management Platform</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold {{ $invoice->status === 'paid' ? 'text-emerald-700' : 'text-amber-600' }}">
                    {{ $invoice->status === 'paid' ? 'RECEIPT' : 'INVOICE' }}
                </p>
                <p class="text-sm text-gray-500 mt-1">{{ $invoice->status === 'paid' ? $invoice->receipt_number : $invoice->invoice_number }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $invoice->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- Billed To --}}
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-2">Billed To</p>
                <p class="text-sm font-semibold text-gray-800">{{ tenant('name') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ tenant('id') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-2">{{ $invoice->status === 'paid' ? 'Payment Date' : 'Due Date' }}</p>
                <p class="text-sm font-semibold text-gray-800">
                    {{ $invoice->status === 'paid' ? $invoice->paid_at->format('M d, Y') : $invoice->due_date->format('M d, Y') }}
                </p>
                @php $colors = ['unpaid' => 'bg-amber-50 text-amber-600', 'paid' => 'bg-emerald-50 text-emerald-700', 'cancelled' => 'bg-red-50 text-red-600']; @endphp
                <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$invoice->status] ?? '' }} capitalize mt-1 inline-block">{{ $invoice->status }}</span>
            </div>
        </div>

        {{-- Items --}}
        <table class="w-full mb-6">
            <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="text-left text-xs text-gray-400 font-medium py-3">Description</th>
                    <th class="text-right text-xs text-gray-400 font-medium py-3">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-50">
                    <td class="py-3">
                        <p class="text-sm font-medium text-gray-800">{{ $plan->name }} Plan Subscription</p>
                        <p class="text-xs text-gray-400">{{ ucfirst($invoice->cycle) }} billing cycle</p>
                    </td>
                    <td class="py-3 text-right text-sm text-gray-800">KES {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                <tr class="border-b border-gray-50">
                    <td class="py-3 text-sm text-emerald-600">Discount</td>
                    <td class="py-3 text-right text-sm text-emerald-600">- KES {{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="border-b border-gray-50">
                    <td class="py-3 text-sm text-gray-600">VAT ({{ $invoice->vat_percentage }}%)</td>
                    <td class="py-3 text-right text-sm text-gray-600">KES {{ number_format($invoice->vat_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="py-3 text-base font-bold text-gray-800">Total</td>
                    <td class="py-3 text-right text-base font-bold text-emerald-700">KES {{ number_format($invoice->total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($invoice->status !== 'paid')
        {{-- Payment Instructions --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <p class="text-xs font-semibold text-gray-700 mb-2">Payment Instructions</p>
            <div class="grid grid-cols-2 gap-4 text-xs text-gray-600">
                <div>
                    <p class="font-medium mb-1">M-Pesa Paybill:</p>
                    <p>Paybill: {{ \App\Models\Central\DarajaSetting::getSettings()->paybill_number ?? 'N/A' }}</p>
                    <p>Account: {{ tenant()->slug ?? tenant('id') }}</p>
                </div>
                <div>
                    <p class="font-medium mb-1">Bank Transfer:</p>
                    <p>KCB Bank Kenya</p>
                    <p>Acc: 1234567890</p>
                    <p>Ref: {{ tenant()->slug ?? tenant('id') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="border-t border-gray-100 pt-4 text-center">
            <p class="text-xs text-gray-400">Thank you for using {{ isset($branding) ? $branding->platform_name : 'OpEx HRIS' }}</p>
            <p class="text-xs text-gray-300 mt-1">Generated on {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>
</div>

<style>
@media print {
    aside, header, .page-actions { display: none !important; }
    #invoice-content { border: none !important; box-shadow: none !important; }
}
</style>
@endsection

