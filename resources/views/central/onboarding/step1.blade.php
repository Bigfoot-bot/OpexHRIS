@extends('central.layouts.app')

@section('page-title', 'New Facility — Step 1 of 5')
@section('page-subtitle', 'Facility Profile')

@section('content')

    {{-- Progress --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1,2,3,4,5] as $step)
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                {{ $step === 1 ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-400' }}">
                {{ $step }}
            </div>
            @if($step < 5)
            <div class="w-12 h-0.5 {{ $step < 1 ? 'bg-emerald-700' : 'bg-gray-100' }}"></div>
            @endif
        </div>
        @endforeach
        <div class="ml-3 text-sm text-gray-500">Facility Profile</div>
    </div>

    <div class="max-w-2xl">

        @if($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.onboarding.step1.store') }}">
            @csrf

            <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
                <h2 class="text-sm font-medium text-emerald-900 mb-5">Basic Information</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Facility Name *</label>
                            <input type="text" name="name" value="{{ old('name', session('wizard.step1.name')) }}" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Facility Type *</label>
                            <select name="facility_type" required
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Type</option>
                                <option value="hospital" {{ old('facility_type', session('wizard.step1.facility_type')) === 'hospital' ? 'selected' : '' }}>Hospital</option>
                                <option value="clinic" {{ old('facility_type', session('wizard.step1.facility_type')) === 'clinic' ? 'selected' : '' }}>Clinic</option>
                                <option value="health_centre" {{ old('facility_type', session('wizard.step1.facility_type')) === 'health_centre' ? 'selected' : '' }}>Health Centre</option>
                                <option value="dispensary" {{ old('facility_type', session('wizard.step1.facility_type')) === 'dispensary' ? 'selected' : '' }}>Dispensary</option>
                                <option value="nursing_home" {{ old('facility_type', session('wizard.step1.facility_type')) === 'nursing_home' ? 'selected' : '' }}>Nursing Home</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email *</label>
                            <input type="email" name="email" value="{{ old('email', session('wizard.step1.email')) }}" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone *</label>
                            <input type="text" name="phone" value="{{ old('phone', session('wizard.step1.phone')) }}" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">County *</label>
                            <input type="text" name="county" value="{{ old('county', session('wizard.step1.county')) }}" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">KEPH Level</label>
                            <select name="keph_level"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Level</option>
                                @foreach(['1','2','3','4','5','6'] as $level)
                                <option value="level_{{ $level }}" {{ old('keph_level', session('wizard.step1.keph_level')) === 'level_'.$level ? 'selected' : '' }}>
                                    Level {{ $level }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Bed Capacity</label>
                            <input type="number" name="bed_capacity" value="{{ old('bed_capacity', session('wizard.step1.bed_capacity')) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Subscription Plan *</label>
                            <select name="subscription_plan" id="plan_select" required
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Plan</option>
                                @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" data-price="{{ $plan->monthly_price }}"
                                    {{ (string) old('subscription_plan', session('wizard.step1.subscription_plan')) === (string) $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} — KES {{ number_format($plan->monthly_price) }}/mo
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Billing Cycle --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Billing Cycle *</label>
                        <div class="grid grid-cols-4 gap-3" id="cycle_options">
                            @php
                                $cycles = [
                                    'monthly'   => ['label' => 'Monthly',   'months' => 1,  'discount' => 0,                                                                          'color' => 'gray'],
                                    'quarterly' => ['label' => 'Quarterly', 'months' => 3,  'discount' => (float)($discounts['quarterly']->discount_percentage ?? 10), 'color' => 'blue'],
                                    'biannual'  => ['label' => '6 Months',  'months' => 6,  'discount' => (float)($discounts['biannual']->discount_percentage  ?? 15), 'color' => 'purple'],
                                    'annual'    => ['label' => 'Annual',    'months' => 12, 'discount' => (float)($discounts['annual']->discount_percentage    ?? 20), 'color' => 'emerald'],
                                ];
                                $selectedCycle = old('billing_cycle', session('wizard.step1.billing_cycle', 'monthly'));
                                $colorMap = [
                                    'gray'    => ['border' => 'border-gray-200',   'bg' => 'bg-gray-50',    'text' => 'text-gray-700',   'badge' => 'bg-gray-100 text-gray-500',     'ring' => 'ring-gray-400'],
                                    'blue'    => ['border' => 'border-blue-200',   'bg' => 'bg-blue-50',    'text' => 'text-blue-700',   'badge' => 'bg-blue-100 text-blue-600',     'ring' => 'ring-blue-400'],
                                    'purple'  => ['border' => 'border-purple-200', 'bg' => 'bg-purple-50',  'text' => 'text-purple-700', 'badge' => 'bg-purple-100 text-purple-600', 'ring' => 'ring-purple-400'],
                                    'emerald' => ['border' => 'border-emerald-200','bg' => 'bg-emerald-50', 'text' => 'text-emerald-700','badge' => 'bg-emerald-100 text-emerald-600','ring' => 'ring-emerald-500'],
                                ];
                            @endphp
                            @foreach($cycles as $key => $cycle)
                            @php $c = $colorMap[$cycle['color']]; @endphp
                            <label class="cycle-card cursor-pointer border-2 rounded-xl p-3 text-center transition-all
                                {{ $selectedCycle === $key ? 'border-emerald-500 bg-emerald-50' : 'border-gray-100 bg-white hover:border-gray-200' }}"
                                data-cycle="{{ $key }}" data-months="{{ $cycle['months'] }}" data-discount="{{ $cycle['discount'] }}">
                                <input type="radio" name="billing_cycle" value="{{ $key }}" class="sr-only"
                                    {{ $selectedCycle === $key ? 'checked' : '' }}>
                                <p class="text-xs font-semibold text-gray-700 mb-1">{{ $cycle['label'] }}</p>
                                @if($cycle['discount'] > 0)
                                    <span class="text-xs bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">{{ $cycle['discount'] }}% off</span>
                                @else
                                    <span class="text-xs bg-gray-100 text-gray-400 px-1.5 py-0.5 rounded-full">Base price</span>
                                @endif
                                <p class="text-xs text-gray-400 mt-1.5 cycle-price">—</p>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Pricing shown is total for the billing period (trial — no charge during onboarding).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Address</label>
                        <input type="text" name="address" value="{{ old('address', session('wizard.step1.address')) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.tenants.index') }}" class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Next: Departments →
                </button>
            </div>

        </form>
    </div>

<script>
    const planSelect  = document.getElementById('plan_select');
    const cycleCards  = document.querySelectorAll('.cycle-card');

    function formatKES(n) {
        return 'KES ' + Math.round(n).toLocaleString();
    }

    function updatePrices() {
        const opt = planSelect.options[planSelect.selectedIndex];
        const monthly = parseFloat(opt?.dataset?.price || 0);

        cycleCards.forEach(card => {
            const months   = parseInt(card.dataset.months);
            const discount = parseFloat(card.dataset.discount);
            const total    = monthly * months * (1 - discount / 100);
            card.querySelector('.cycle-price').textContent = monthly > 0 ? formatKES(total) : '—';
        });
    }

    function selectCycle(card) {
        cycleCards.forEach(c => {
            c.classList.remove('border-emerald-500', 'bg-emerald-50');
            c.classList.add('border-gray-100', 'bg-white');
            c.querySelector('input[type=radio]').checked = false;
        });
        card.classList.add('border-emerald-500', 'bg-emerald-50');
        card.classList.remove('border-gray-100', 'bg-white');
        card.querySelector('input[type=radio]').checked = true;
    }

    cycleCards.forEach(card => {
        card.addEventListener('click', () => selectCycle(card));
    });

    planSelect.addEventListener('change', updatePrices);
    updatePrices();
</script>

@endsection
