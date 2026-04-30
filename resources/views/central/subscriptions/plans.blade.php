@extends('central.layouts.app')

@section('page-title', 'Subscription Plans')
@section('page-subtitle', 'Manage plans, pricing and discounts')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- Billing Cycle Discounts --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Billing Cycle Discounts</h2>
        <form method="POST" action="{{ route('admin.subscription-plans.discounts') }}">
            @csrf
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Monthly</label>
                    <div class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-400">0% (Base price)</div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Quarterly (3 months)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="quarterly_discount" value="{{ $discounts['quarterly']->discount_percentage ?? 10 }}" min="0" max="100" step="0.1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <span class="text-sm text-gray-400">%</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">6 Months</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="biannual_discount" value="{{ $discounts['biannual']->discount_percentage ?? 15 }}" min="0" max="100" step="0.1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <span class="text-sm text-gray-400">%</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Annual (12 months)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="annual_discount" value="{{ $discounts['annual']->discount_percentage ?? 20 }}" min="0" max="100" step="0.1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <span class="text-sm text-gray-400">%</span>
                    </div>
                </div>
            </div>
            <button type="submit" class="mt-4 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Save Discounts
            </button>
        </form>
    </div>

    {{-- Add New Plan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Add New Plan</h2>
        <form method="POST" action="{{ route('admin.subscription-plans.store') }}">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Plan Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Basic, Standard, Premium"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Monthly Price (KES) *</label>
                    <input type="number" name="monthly_price" required min="0" placeholder="e.g. 5000"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Max Employees *</label>
                    <input type="number" name="max_employees" required min="1" placeholder="e.g. 50"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                    <input type="text" name="description" placeholder="Brief description"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 text-emerald-600 rounded border-gray-300"/>
                    <label class="text-sm text-gray-600">Mark as Featured</label>
                </div>
            </div>
            <button type="submit" class="mt-4 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Add Plan
            </button>
        </form>
    </div>

    {{-- Existing Plans --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Existing Plans</h2>
        @if($plans->isEmpty())
            <p class="text-center text-gray-400 text-sm py-8">No plans yet.</p>
        @else
            @php
                $qDiscount  = (float) ($discounts['quarterly']->discount_percentage ?? 10);
                $bDiscount  = (float) ($discounts['biannual']->discount_percentage  ?? 15);
                $aDiscount  = (float) ($discounts['annual']->discount_percentage    ?? 20);
            @endphp
            <div class="space-y-6">
                @foreach($plans as $plan)
                <div class="border border-gray-100 rounded-xl p-5">

                    {{-- Plan header --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold text-gray-800">{{ $plan->name }}</span>
                            @if($plan->is_featured)
                                <span class="text-xs bg-amber-50 text-amber-600 border border-amber-100 px-2 py-0.5 rounded-full">Featured</span>
                            @endif
                            @if(!$plan->is_active)
                                <span class="text-xs bg-red-50 text-red-600 border border-red-100 px-2 py-0.5 rounded-full">Inactive</span>
                            @else
                                <span class="text-xs bg-emerald-50 text-emerald-600 border border-emerald-100 px-2 py-0.5 rounded-full">Active</span>
                            @endif
                        </div>
                        <p class="text-lg font-bold text-emerald-700">KES {{ number_format($plan->monthly_price, 0) }}<span class="text-xs text-gray-400">/mo</span></p>
                    </div>
                    <p class="text-xs text-gray-400 mb-4">{{ $plan->description }} &middot; Max {{ $plan->max_employees }} employees</p>

                    {{-- Cycle pricing breakdown --}}
                    <div class="grid grid-cols-4 gap-3 mb-4">
                        @php
                            $monthly   = $plan->monthly_price * 1;
                            $quarterly = $plan->monthly_price * 3  * (1 - $qDiscount / 100);
                            $biannual  = $plan->monthly_price * 6  * (1 - $bDiscount / 100);
                            $annual    = $plan->monthly_price * 12 * (1 - $aDiscount / 100);
                        @endphp
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-400 mb-1">Monthly</p>
                            <p class="text-sm font-semibold text-gray-800">KES {{ number_format($monthly, 0) }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">No discount</p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-blue-400 mb-1">Quarterly</p>
                            <p class="text-sm font-semibold text-blue-700">KES {{ number_format($quarterly, 0) }}</p>
                            <p class="text-xs text-blue-400 mt-0.5">{{ $qDiscount }}% off · 3 months</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-purple-400 mb-1">6 Months</p>
                            <p class="text-sm font-semibold text-purple-700">KES {{ number_format($biannual, 0) }}</p>
                            <p class="text-xs text-purple-400 mt-0.5">{{ $bDiscount }}% off · 6 months</p>
                        </div>
                        <div class="bg-emerald-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-emerald-500 mb-1">Annual</p>
                            <p class="text-sm font-semibold text-emerald-700">KES {{ number_format($annual, 0) }}</p>
                            <p class="text-xs text-emerald-500 mt-0.5">{{ $aDiscount }}% off · 12 months</p>
                        </div>
                    </div>

                    {{-- Update form --}}
                    <form method="POST" action="{{ route('admin.subscription-plans.update', $plan) }}" class="grid grid-cols-3 gap-3">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $plan->name }}" required placeholder="Name"
                               class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <input type="number" name="monthly_price" value="{{ $plan->monthly_price }}" required min="0" placeholder="Monthly price"
                               class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <input type="number" name="max_employees" value="{{ $plan->max_employees }}" required min="1" placeholder="Max employees"
                               class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <input type="text" name="description" value="{{ $plan->description }}" placeholder="Description"
                               class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 col-span-2"/>
                        <select name="is_active" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="1" {{ $plan->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$plan->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <div class="col-span-3">
                            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-medium py-2 rounded-lg">Update Plan</button>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
