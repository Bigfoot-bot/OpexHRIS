@extends('central.layouts.app')

@section('page-title', $invoice->invoice_number)
@section('page-subtitle', $invoice->tenant->name . ' — ' . ucfirst($invoice->status))

@section('page-actions')
    <a href="{{ route('admin.invoices.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Invoices
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

<div class="grid grid-cols-3 gap-5">

    {{-- Invoice --}}
    <div class="col-span-2 space-y-5">

        <div class="bg-white rounded-xl border border-green-100 p-8">

            {{-- Header --}}
            <div class="flex items-start justify-between mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 bg-emerald-600 rounded"></div>
                        <span class="text-sm font-medium text-emerald-900">OpEx HRIS</span>
                    </div>
                    <p class="text-xs text-gray-400">Healthcare HR Management Platform</p>
                    <p class="text-xs text-gray-400">Nairobi, Kenya</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-medium text-emerald-900">{{ $invoice->invoice_number }}</p>
                    <p class="text-xs text-gray-400 mt-1">Issue Date: {{ $invoice->issue_date->format('M d, Y') }}</p>
                    <p class="text-xs text-gray-400">Due Date: {{ $invoice->due_date->format('M d, Y') }}</p>
                    @php
                        $statusColors = [
                            'paid'      => 'bg-emerald-50 text-emerald-600',
                            'sent'      => 'bg-blue-50 text-blue-600',
                            'draft'     => 'bg-amber-50 text-amber-600',
                            'overdue'   => 'bg-red-50 text-red-500',
                            'cancelled' => 'bg-gray-50 text-gray-500',
                        ];
                    @endphp
                    <span class="inline-block mt-2 text-xs px-2.5 py-1 rounded-full {{ $statusColors[$invoice->status] ?? '' }} capitalize">
                        {{ $invoice->status }}
                    </span>
                </div>
            </div>

            {{-- Bill To --}}
            <div class="mb-8">
                <p class="text-xs text-gray-400 mb-2 uppercase tracking-widest">Bill To</p>
                <p class="text-sm font-medium text-emerald-900">{{ $invoice->tenant->name }}</p>
                <p class="text-xs text-gray-400">{{ $invoice->tenant->email }}</p>
                <p class="text-xs text-gray-400">{{ $invoice->tenant->phone }}</p>
                <p class="text-xs text-gray-400">{{ $invoice->tenant->slug }}.hris-platform.test</p>
            </div>

            {{-- Line Items --}}
            <table class="w-full mb-6">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs text-gray-400 font-medium py-2">Description</th>
                        <th class="text-right text-xs text-gray-400 font-medium py-2">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-50">
                        <td class="py-3">
                            <p class="text-sm text-gray-700 capitalize">{{ $invoice->subscription_plan }} Plan — Monthly Subscription</p>
                            <p class="text-xs text-gray-400">{{ $invoice->issue_date->format('F Y') }}</p>
                        </td>
                        <td class="py-3 text-right text-sm text-gray-700">KES {{ number_format($invoice->amount) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="border-b border-gray-50">
                        <td class="py-2 text-xs text-gray-400">Subtotal</td>
                        <td class="py-2 text-right text-sm text-gray-600">KES {{ number_format($invoice->amount) }}</td>
                    </tr>
                    <tr class="border-b border-gray-50">
                        <td class="py-2 text-xs text-gray-400">VAT (16%)</td>
                        <td class="py-2 text-right text-sm text-gray-600">KES {{ number_format($invoice->tax) }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-sm font-medium text-emerald-900">Total</td>
                        <td class="py-3 text-right text-lg font-medium text-emerald-900">KES {{ number_format($invoice->total) }}</td>
                    </tr>
                </tfoot>
            </table>

            @if($invoice->paid_date)
            <div class="bg-emerald-50 rounded-lg p-4">
                <p class="text-xs text-emerald-700 font-medium">✅ Paid on {{ $invoice->paid_date->format('M d, Y') }}</p>
                <p class="text-xs text-emerald-600">Method: {{ $invoice->payment_method }} {{ $invoice->payment_reference ? '· Ref: ' . $invoice->payment_reference : '' }}</p>
            </div>
            @endif

            @if($invoice->notes)
            <div class="mt-4 pt-4 border-t border-gray-50">
                <p class="text-xs text-gray-400 mb-1">Notes</p>
                <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
            </div>
            @endif

        </div>

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Mark as Paid --}}
        @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Mark as Paid</h2>
            <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Payment Method *</label>
                        <select name="payment_method" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select</option>
                            <option value="M-Pesa">M-Pesa</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Reference</label>
                        <input type="text" name="payment_reference"
                               placeholder="e.g. MPESA code"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <button type="submit"
                            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                        Mark as Paid
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Invoice Details --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Details</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Invoice No.</span>
                    <span class="text-sm font-mono text-gray-700">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Plan</span>
                    <span class="text-sm text-gray-700 capitalize">{{ $invoice->subscription_plan }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Amount</span>
                    <span class="text-sm text-gray-700">KES {{ number_format($invoice->amount) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">VAT</span>
                    <span class="text-sm text-gray-700">KES {{ number_format($invoice->tax) }}</span>
                </div>
                <div class="flex items-center justify-between border-t border-gray-50 pt-2">
                    <span class="text-xs font-medium text-gray-600">Total</span>
                    <span class="text-sm font-medium text-emerald-900">KES {{ number_format($invoice->total) }}</span>
                </div>
            </div>
        </div>

        {{-- Delete --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}"
                  onsubmit="return confirm('Delete this invoice?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full text-sm bg-red-50 text-red-500 hover:bg-red-100 py-2 rounded-lg transition-colors">
                    Delete Invoice
                </button>
            </form>
        </div>

    </div>

</div>

@endsection