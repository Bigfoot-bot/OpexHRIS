@extends('central.layouts.app')

@section('page-title', 'Invoices')
@section('page-subtitle', 'Billing and subscription tracking')

@section('page-actions')
    {{-- Generate Invoice --}}
    <form method="POST" action="{{ route('admin.invoices.store') }}" class="flex items-center gap-2">
        @csrf
        <select name="tenant_id"
                class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Select Facility</option>
            @foreach(\App\Models\Central\Tenant::where('is_active', true)->get() as $tenant)
                <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            Generate Invoice
        </button>
    </form>
    <form method="POST" action="{{ route('admin.invoices.mark-overdue') }}">
        @csrf
        <button type="submit"
                class="bg-red-50 text-red-500 hover:bg-red-100 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            Mark Overdue
        </button>
    </form>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Revenue</p>
            <p class="text-xl font-medium text-emerald-900">KES {{ number_format($stats['total_revenue']) }}</p>
            <p class="text-xs text-emerald-600 mt-1">From paid invoices</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Pending Value</p>
            <p class="text-xl font-medium text-amber-600">KES {{ number_format($stats['pending_value']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['pending'] }} invoices</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Paid</p>
            <p class="text-xl font-medium text-emerald-600">{{ $stats['paid'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Invoices paid</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Overdue</p>
            <p class="text-xl font-medium text-red-500">{{ $stats['overdue'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Require follow up</p>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($invoices->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No invoices yet.</p>
                <p class="text-xs text-gray-400 mt-1">Generate an invoice for a facility above.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Invoice</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Facility</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Plan</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Amount</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Due Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $invoice->invoice_number }}</p>
                            <p class="text-xs text-gray-400">{{ $invoice->issue_date->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-700">{{ $invoice->tenant->name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $planColors = [
                                    'basic'        => 'bg-gray-50 text-gray-500',
                                    'professional' => 'bg-blue-50 text-blue-600',
                                    'enterprise'   => 'bg-emerald-50 text-emerald-600',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $planColors[$invoice->subscription_plan] ?? '' }} capitalize">
                                {{ $invoice->subscription_plan }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-700">KES {{ number_format($invoice->total) }}</p>
                            <p class="text-xs text-gray-400">+VAT {{ number_format($invoice->tax) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-500' : 'text-gray-600' }}">
                                {{ $invoice->due_date->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'paid'      => 'bg-emerald-50 text-emerald-600',
                                    'sent'      => 'bg-blue-50 text-blue-600',
                                    'draft'     => 'bg-amber-50 text-amber-600',
                                    'overdue'   => 'bg-red-50 text-red-500',
                                    'cancelled' => 'bg-gray-50 text-gray-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$invoice->status] ?? '' }} capitalize">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.invoices.show', $invoice) }}"
                                   class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                @if($invoice->status !== 'paid')
                                    <a href="{{ route('admin.invoices.show', $invoice) }}"
                                       class="text-xs text-blue-500 hover:text-blue-700">Mark Paid</a>
                                @endif
                                <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}"
                                      onsubmit="return confirm('Delete this invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($invoices->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $invoices->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection