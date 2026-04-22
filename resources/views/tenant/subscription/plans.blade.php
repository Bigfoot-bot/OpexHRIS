@extends('tenant.layouts.app')

@section('page-title', 'Subscription Plans')
@section('page-subtitle', 'Choose the right plan for your facility')

@section('content')
<div class="space-y-6">

    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>
    @endif

    @if($isHighestPlan && !$canRenewSame)
    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5 text-center">
        <p class="text-emerald-700 font-semibold">You are on the highest available plan!</p>
        <p class="text-emerald-600 text-sm mt-1">Your current <strong>{{ $currentPlanName }}</strong> plan cannot be upgraded. Wait for it to expire to resubscribe.</p>
        <a href="{{ route('tenant.subscription.index') }}" class="mt-3 inline-block text-sm text-emerald-700 underline">View my subscription</a>
    </div>
    @elseif($canRenewSame)
    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
        <p class="text-amber-700 font-semibold">Your subscription expires in {{ $daysLeft }} days!</p>
        <p class="text-amber-600 text-sm mt-1">You can now renew your current <strong>{{ $currentPlanName }}</strong> plan or upgrade to a higher plan.</p>
    </div>
    @elseif($currentPlanName)
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
        <p class="text-blue-700 font-semibold">Upgrading from {{ $currentPlanName }}</p>
        <p class="text-blue-600 text-sm mt-1">You can only upgrade to a higher plan. Same plan renewal is available 7 days before expiry.</p>
    </div>
    @endif

    {{-- Billing Cycle Selector --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center justify-center gap-2">
        @foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'biannual' => '6 Months', 'annual' => 'Annual'] as $key => $label)
        <button onclick="setCycle('{{ $key }}')" id="cycle-{{ $key }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cycle-btn">
            {{ $label }}
            @if($key !== 'monthly' && isset($discounts[$key]))
                <span class="ml-1 text-xs bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">
                    -{{ number_format($discounts[$key]->discount_percentage, 0) }}%
                </span>
            @endif
        </button>
        @endforeach
    </div>

    @if($plans->isEmpty())
    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 text-center">
        <p class="text-amber-700 font-semibold">No plans available for upgrade.</p>
        <p class="text-amber-600 text-sm mt-1">Please contact support.</p>
    </div>
    @endif

    {{-- Plans Grid --}}
    <div class="grid grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white rounded-2xl border {{ $plan->is_featured ? 'border-emerald-400 shadow-lg' : 'border-gray-100 shadow-sm' }} p-6 relative">
            @if($plan->is_featured)
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                    <span class="bg-emerald-700 text-white text-xs font-medium px-3 py-1 rounded-full">Most Popular</span>
                </div>
            @endif
            <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $plan->name }}</h3>
            <p class="text-xs text-gray-400 mb-4">{{ $plan->description }}</p>

            @foreach(['monthly', 'quarterly', 'biannual', 'annual'] as $cycle)
            @php
                $discount = $discounts[$cycle]->discount_percentage ?? 0;
                $months   = ['monthly' => 1, 'quarterly' => 3, 'biannual' => 6, 'annual' => 12][$cycle];
                $subtotal = $plan->monthly_price * $months;
                $discAmt  = $subtotal * ($discount / 100);
                $after    = $subtotal - $discAmt;
                $vat      = $after * ($settings->vat_percentage / 100);
                $total    = $after + $vat;
            @endphp
            <div class="plan-price hidden" data-cycle="{{ $cycle }}" data-plan="{{ $plan->id }}">
                <div class="text-3xl font-bold text-emerald-700 mb-1">
                    KES {{ number_format($total, 0) }}
                </div>
                <p class="text-xs text-gray-400 mb-1">per {{ $months }} month{{ $months > 1 ? 's' : '' }} (incl. {{ $settings->vat_percentage }}% VAT)</p>
                @if($discount > 0)
                    <p class="text-xs text-emerald-600 mb-4">Save KES {{ number_format($discAmt, 0) }} ({{ number_format($discount, 0) }}% off)</p>
                @else
                    <p class="text-xs text-gray-300 mb-4">&nbsp;</p>
                @endif
            </div>
            @endforeach

            <ul class="space-y-2 mb-6">
                <li class="text-xs text-gray-600 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Up to {{ $plan->max_employees }} employees
                </li>
                <li class="text-xs text-gray-600 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Full HRIS features
                </li>
                <li class="text-xs text-gray-600 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Email support
                </li>
            </ul>

            <form method="POST" action="{{ route('tenant.subscription.checkout') }}">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}"/>
                <input type="hidden" name="cycle" class="cycle-input" value="monthly"/>
                <button type="submit"
                        class="w-full {{ $plan->is_featured ? 'bg-emerald-700 hover:bg-emerald-800 text-white' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }} text-sm font-medium py-2.5 rounded-lg transition-colors">
                    Get Started
                </button>
            </form>
        </div>
        @endforeach
    </div>

</div>

<script>
let currentCycle = 'monthly';

function setCycle(cycle) {
    currentCycle = cycle;
    document.querySelectorAll('.cycle-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-700', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-600');
    });
    document.getElementById('cycle-' + cycle).classList.remove('bg-gray-100', 'text-gray-600');
    document.getElementById('cycle-' + cycle).classList.add('bg-emerald-700', 'text-white');

    document.querySelectorAll('.plan-price').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[data-cycle="' + cycle + '"]').forEach(el => el.classList.remove('hidden'));
    document.querySelectorAll('.cycle-input').forEach(el => el.value = cycle);
}

setCycle('monthly');
</script>
@endsection
