@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'My Subscription')
@section('page-subtitle', 'Manage your facility subscription')

@section('page-actions')
    <a href="{{ route('tenant.subscription.plans') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
        Renew / Upgrade
    </a>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>
    @endif

    {{-- Current Subscription --}}
    @if($subscription && in_array($subscription->status, ['active', 'trial']))
    <div class="bg-emerald-700 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs opacity-70 uppercase tracking-widest">Current Plan</p>
                <p class="text-2xl font-bold mt-1">{{ $subscription->plan->name ?? 'N/A' }}</p>
                <p class="text-xs opacity-60 mt-2">{{ ucfirst($subscription->cycle) }} billing &middot; Expires {{ $subscription->end_date->format('M d, Y') }}</p>
            </div>
            <div class="text-right">
                @php $colors = ['active' => 'bg-white/20 text-white', 'trial' => 'bg-blue-400/30 text-white', 'expired' => 'bg-red-400/30 text-white', 'suspended' => 'bg-gray-400/30 text-white']; @endphp
                <span class="text-xs px-3 py-1.5 rounded-full {{ $colors[$subscription->status] ?? '' }} capitalize">{{ $subscription->status }}</span>
                <p class="text-xs opacity-60 mt-2">{{ $subscription->daysRemaining() }} days remaining</p>
            </div>
        </div>
        @if($subscription->daysRemaining() <= 7 && $subscription->status === 'active')
            <div class="mt-4 bg-white/10 rounded-lg px-4 py-2 text-xs">
                <span>Your subscription expires in <strong>{{ $subscription->daysRemaining() }}</strong> days. Please renew to avoid service interruption.</span>
            </div>
        @endif
    </div>
    @else
    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6 text-center">
        <p class="text-amber-700 font-medium">No active subscription</p>
        <p class="text-amber-600 text-sm mt-1">Choose a plan to get started</p>
        <a href="{{ route('tenant.subscription.plans') }}" class="mt-3 inline-block bg-emerald-700 text-white text-sm font-medium px-5 py-2 rounded-lg">View Plans</a>
    </div>
    @endif

    {{-- Recent Payments --}}
    @if($payments->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Recent Payments</h2>
        <div class="space-y-3">
            @foreach($payments as $payment)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div>
                    <p class="text-sm font-medium text-gray-800">KES {{ number_format($payment->amount, 2) }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} &middot; {{ $payment->created_at->format('M d, Y') }}</p>
                </div>
                @php $colors = ['pending' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-red-50 text-red-600']; @endphp
                <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$payment->status] ?? '' }} capitalize">{{ $payment->status }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Invoices --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Invoices & Receipts</h2>
        @if($invoices->isEmpty())
            <p class="text-center text-gray-400 text-sm py-8">No invoices yet.</p>
    @elseif($subscription && $subscription->status === 'suspended')
    <div class="bg-red-50 border border-red-100 rounded-2xl p-6 text-center">
        <p class="text-red-700 font-medium text-lg">Subscription Suspended</p>
        <p class="text-red-500 text-sm mt-1">Your subscription has been suspended. Please contact support or renew.</p>
        <a href="{{ route('tenant.subscription.plans') }}" class="mt-3 inline-block bg-emerald-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Renew Now</a>
    </div>
    @elseif($subscription && $subscription->status === 'cancelled')
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 text-center">
        <p class="text-gray-700 font-medium text-lg">Subscription Cancelled</p>
        <p class="text-gray-500 text-sm mt-1">Your subscription has been cancelled by the administrator. Please contact support.</p>
        <a href="{{ route('tenant.subscription.plans') }}" class="mt-3 inline-block bg-emerald-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Subscribe Again</a>
    </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Invoice No.</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Plan</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Total</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $invoice->plan->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-emerald-700">KES {{ number_format($invoice->total, 2) }}</td>
                        <td class="px-4 py-3">
                            @php $colors = ['unpaid' => 'bg-amber-50 text-amber-600', 'paid' => 'bg-emerald-50 text-emerald-700', 'cancelled' => 'bg-red-50 text-red-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$invoice->status] ?? '' }} capitalize">{{ $invoice->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $invoice->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('tenant.subscription.invoice', $invoice) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">
                                {{ $invoice->status === 'paid' ? 'View Receipt' : 'View Invoice' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $invoices->links() }}</div>
        @endif
    </div>

</div>
@endsection



