@extends('central.layouts.app')

@section('page-title', 'Subscription Settings')
@section('page-subtitle', 'VAT, trial period and facility subscriptions')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- System Settings --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">System Settings</h2>
        <form method="POST" action="{{ route('admin.subscription-settings.update') }}">
            @csrf
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">VAT Percentage (%)</label>
                    <input type="number" name="vat_percentage" value="{{ $settings->vat_percentage }}" min="0" max="100" step="0.1" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Trial Days</label>
                    <input type="number" name="trial_days" value="{{ $settings->trial_days }}" min="0" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Grace Period Days</label>
                    <input type="number" name="grace_period_days" value="{{ $settings->grace_period_days }}" min="0" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Invoice Prefix</label>
                    <input type="text" name="invoice_prefix" value="{{ $settings->invoice_prefix }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Receipt Prefix</label>
                    <input type="text" name="receipt_prefix" value="{{ $settings->receipt_prefix }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
            </div>
            <button type="submit" class="mt-4 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Save Settings
            </button>
        </form>
    </div>

    {{-- Facility Subscriptions --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Facility Subscriptions</h2>
        @if($subscriptions->isEmpty())
            <p class="text-center text-gray-400 text-sm py-8">No subscriptions yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Facility</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Plan</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Cycle</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Expires</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($subscriptions as $sub)
                    @php $tenant = DB::table('tenants')->where('id', $sub->tenant_id)->first(); @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $tenant->name ?? $sub->tenant_id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sub->plan->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 capitalize">{{ $sub->cycle }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sub->end_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            @php $colors = ['active' => 'bg-emerald-50 text-emerald-700', 'trial' => 'bg-blue-50 text-blue-600', 'expired' => 'bg-red-50 text-red-600', 'suspended' => 'bg-gray-50 text-gray-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$sub->status] ?? '' }} capitalize">{{ $sub->status }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.subscription-settings.extend', $sub) }}" class="flex gap-1">
                                    @csrf
                                    <input type="number" name="days" min="1" placeholder="Days" class="w-16 px-2 py-1 rounded border border-gray-200 text-xs"/>
                                    <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Extend</button>
                                </form>
                                <form method="POST" action="{{ route('admin.subscription-settings.suspend', $sub) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium"
                                            onclick="return confirm('Suspend this subscription?')">Suspend</button>
                                </form>
                                <form method="POST" action="{{ route('admin.subscription-settings.cancel', $sub) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-gray-500 hover:text-gray-700 font-medium"
                                            onclick="return confirm('Cancel this subscription? The facility will lose access.')">Cancel</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $subscriptions->links() }}</div>
        @endif
    </div>

</div>
@endsection




