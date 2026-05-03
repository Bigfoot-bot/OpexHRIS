@extends('central.layouts.app')
@section('title', 'Statutory Deductions')

@section('content')
<div class="space-y-5">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.statutory-deductions.update') }}" class="space-y-5">
        @csrf

        {{-- PAYE --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">PAYE — Pay As You Earn</h2>
                    <p class="text-xs text-gray-400">Graduated tax bands. Set the upper salary limit and rate for each band.</p>
                </div>
            </div>
            <div class="px-5 py-4 space-y-3">

                {{-- Column headers --}}
                <div class="flex items-center gap-3 px-1">
                    <div class="w-16 flex-shrink-0"></div>
                    <div class="flex-1 text-xs font-semibold text-gray-400 uppercase tracking-wide">Upper Limit (KES)</div>
                    <div class="w-28 text-xs font-semibold text-gray-400 uppercase tracking-wide">Rate</div>
                </div>

                @php
                    $bands = [
                        ['num' => 1, 'label' => 'Band 1'],
                        ['num' => 2, 'label' => 'Band 2'],
                        ['num' => 3, 'label' => 'Band 3'],
                        ['num' => 4, 'label' => 'Band 4'],
                    ];
                @endphp

                @foreach($bands as $band)
                <div class="flex items-center gap-3">
                    <div class="w-16 flex-shrink-0">
                        <span class="inline-block text-xs font-semibold text-blue-600 bg-blue-50 rounded-md px-2 py-1">{{ $band['label'] }}</span>
                    </div>
                    <div class="flex-1">
                        <input type="number" name="paye_band{{ $band['num'] }}_limit"
                               value="{{ old('paye_band'.$band['num'].'_limit', number_format((float)$settings->{'paye_band'.$band['num'].'_limit'}, 0, '.', '')) }}"
                               step="1" min="1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div class="w-28 flex-shrink-0 relative">
                        <input type="number" name="paye_band{{ $band['num'] }}_rate"
                               value="{{ old('paye_band'.$band['num'].'_rate', (float)$settings->{'paye_band'.$band['num'].'_rate'}) }}"
                               step="0.5" min="0" max="100"
                               class="w-full px-3 py-2 pr-7 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">%</span>
                    </div>
                </div>
                @endforeach

                {{-- Band 5 --}}
                <div class="flex items-center gap-3">
                    <div class="w-16 flex-shrink-0">
                        <span class="inline-block text-xs font-semibold text-blue-600 bg-blue-50 rounded-md px-2 py-1">Band 5</span>
                    </div>
                    <div class="flex-1">
                        <div class="px-3 py-2 rounded-lg border border-dashed border-gray-200 bg-gray-50 text-sm text-gray-400">Above Band 4 limit</div>
                    </div>
                    <div class="w-28 flex-shrink-0 relative">
                        <input type="number" name="paye_band5_rate"
                               value="{{ old('paye_band5_rate', (float)$settings->paye_band5_rate) }}"
                               step="0.5" min="0" max="100"
                               class="w-full px-3 py-2 pr-7 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">%</span>
                    </div>
                </div>

                {{-- Personal Relief --}}
                <div class="pt-3 mt-1 border-t border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-16 flex-shrink-0">
                            <span class="text-xs font-semibold text-gray-500">Relief</span>
                        </div>
                        <div class="w-48 flex-shrink-0 relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">KES</span>
                            <input type="number" name="paye_personal_relief"
                                   value="{{ old('paye_personal_relief', (float)$settings->paye_personal_relief) }}"
                                   step="1" min="0"
                                   class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <p class="text-xs text-gray-400 leading-snug">Monthly personal relief deducted from gross tax <span class="text-gray-500 font-medium">(KRA default: KES 2,400/mo)</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom row: SHA + Housing Levy side by side --}}
        <div class="grid grid-cols-2 gap-5">

            {{-- SHA --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">SHA</h2>
                        <p class="text-xs text-gray-400">Social Health Authority (replaces NHIF)</p>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee Contribution Rate</label>
                        <div class="relative">
                            <input type="number" name="sha_rate"
                                   value="{{ old('sha_rate', (float)$settings->sha_rate) }}"
                                   step="0.01" min="0" max="100"
                                   class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">%</span>
                        </div>
                        @error('sha_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1.5">Default: 2.75% of gross salary</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl px-3 py-2.5 text-xs text-emerald-700">
                        KES 50,000 &times; {{ (float)$settings->sha_rate }}% = <strong>KES {{ number_format(50000 * (float)$settings->sha_rate / 100, 2) }}</strong>
                    </div>
                </div>
            </div>

            {{-- Housing Levy --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">Housing Levy</h2>
                        <p class="text-xs text-gray-400">Affordable Housing Fund — both sides contribute</p>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee Rate</label>
                        <div class="relative">
                            <input type="number" name="housing_levy_employee_rate"
                                   value="{{ old('housing_levy_employee_rate', (float)$settings->housing_levy_employee_rate) }}"
                                   step="0.1" min="0" max="100"
                                   class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">%</span>
                        </div>
                        @error('housing_levy_employee_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employer Rate</label>
                        <div class="relative">
                            <input type="number" name="housing_levy_employer_rate"
                                   value="{{ old('housing_levy_employer_rate', (float)$settings->housing_levy_employer_rate) }}"
                                   step="0.1" min="0" max="100"
                                   class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">%</span>
                        </div>
                        @error('housing_levy_employer_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- NSSF --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">NSSF — National Social Security Fund</h2>
                    <p class="text-xs text-gray-400">Two-tier system. Employee and employer each contribute on pensionable pay.</p>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee Rate</label>
                        <div class="relative">
                            <input type="number" name="nssf_employee_rate"
                                   value="{{ old('nssf_employee_rate', (float)$settings->nssf_employee_rate) }}"
                                   step="0.5" min="0" max="100"
                                   class="w-full px-3 py-2 pr-7 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">%</span>
                        </div>
                        @error('nssf_employee_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employer Rate</label>
                        <div class="relative">
                            <input type="number" name="nssf_employer_rate"
                                   value="{{ old('nssf_employer_rate', (float)$settings->nssf_employer_rate) }}"
                                   step="0.5" min="0" max="100"
                                   class="w-full px-3 py-2 pr-7 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">%</span>
                        </div>
                        @error('nssf_employer_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Tier I Limit (KES)</label>
                        <input type="number" name="nssf_tier1_limit"
                               value="{{ old('nssf_tier1_limit', (float)$settings->nssf_tier1_limit) }}"
                               step="1" min="1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        @error('nssf_tier1_limit')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1">Salary up to this amount</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Tier II Limit (KES)</label>
                        <input type="number" name="nssf_tier2_limit"
                               value="{{ old('nssf_tier2_limit', (float)$settings->nssf_tier2_limit) }}"
                               step="1" min="1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        @error('nssf_tier2_limit')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1">Between Tier I and this amount</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Warning + Save --}}
        <div class="flex items-center justify-between gap-6">
            <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 flex-1 text-xs text-amber-700">
                <strong>Note:</strong> Changes apply to future payroll runs only. Existing payslips keep the rates used at the time of processing.
            </div>
            <button type="submit"
                    class="flex-shrink-0 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                Save Rates
            </button>
        </div>

    </form>
</div>
@endsection
