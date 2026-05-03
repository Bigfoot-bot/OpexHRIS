@extends('central.layouts.app')
@section('title', 'Statutory Deductions')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.statutory-deductions.update') }}" class="space-y-6">
        @csrf

        {{-- PAYE --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">PAYE — Pay As You Earn</h2>
                    <p class="text-xs text-gray-400">Graduated income tax bands. Each band applies to income between its lower and upper limit.</p>
                </div>
            </div>
            <div class="px-6 py-5">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide pb-3 w-24">Band</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide pb-3 pr-4">Upper Limit (KES)</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide pb-3 w-40">Rate</th>
                            <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide pb-3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php
                            $bands = [
                                ['num' => 1, 'label' => 'Band 1', 'desc' => 'First slab — lowest earners'],
                                ['num' => 2, 'label' => 'Band 2', 'desc' => 'Second slab'],
                                ['num' => 3, 'label' => 'Band 3', 'desc' => 'Middle income'],
                                ['num' => 4, 'label' => 'Band 4', 'desc' => 'High income'],
                            ];
                        @endphp
                        @foreach($bands as $band)
                        <tr>
                            <td class="py-3 pr-4">
                                <span class="text-xs font-medium text-gray-500">{{ $band['label'] }}</span>
                            </td>
                            <td class="py-3 pr-4">
                                <input type="number" name="paye_band{{ $band['num'] }}_limit"
                                       value="{{ old('paye_band'.$band['num'].'_limit', number_format((float)$settings->{'paye_band'.$band['num'].'_limit'}, 0, '.', '')) }}"
                                       step="1" min="1"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('paye_band'.$band['num'].'_limit') border-red-300 @enderror"/>
                                @error('paye_band'.$band['num'].'_limit')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="py-3 pr-4">
                                <div class="relative w-36">
                                    <input type="number" name="paye_band{{ $band['num'] }}_rate"
                                           value="{{ old('paye_band'.$band['num'].'_rate', (float)$settings->{'paye_band'.$band['num'].'_rate'}) }}"
                                           step="0.5" min="0" max="100"
                                           class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('paye_band'.$band['num'].'_rate') border-red-300 @enderror"/>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                                </div>
                            </td>
                            <td class="py-3 text-xs text-gray-400">{{ $band['desc'] }}</td>
                        </tr>
                        @endforeach
                        {{-- Band 5 — no upper limit --}}
                        <tr>
                            <td class="py-3 pr-4">
                                <span class="text-xs font-medium text-gray-500">Band 5</span>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="px-3 py-2 rounded-lg border border-dashed border-gray-200 text-sm text-gray-400 bg-gray-50">Above Band 4 limit</div>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="relative w-36">
                                    <input type="number" name="paye_band5_rate"
                                           value="{{ old('paye_band5_rate', (float)$settings->paye_band5_rate) }}"
                                           step="0.5" min="0" max="100"
                                           class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('paye_band5_rate') border-red-300 @enderror"/>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                                </div>
                            </td>
                            <td class="py-3 text-xs text-gray-400">Top slab — highest income</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Personal Relief --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-semibold text-gray-500 w-24 flex-shrink-0">Personal Relief</span>
                        <div class="relative w-52">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">KES</span>
                            <input type="number" name="paye_personal_relief"
                                   value="{{ old('paye_personal_relief', (float)$settings->paye_personal_relief) }}"
                                   step="1" min="0"
                                   class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('paye_personal_relief') border-red-300 @enderror"/>
                        </div>
                        <p class="text-xs text-gray-400">Monthly personal relief deducted from gross tax (currently KES 2,400/month per KRA)</p>
                    </div>
                    @error('paye_personal_relief')
                        <p class="text-xs text-red-500 mt-1 ml-28">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- SHA --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">SHA — Social Health Authority</h2>
                    <p class="text-xs text-gray-400">Replaced NHIF in October 2024. Calculated as a percentage of gross salary.</p>
                </div>
            </div>
            <div class="px-6 py-5">
                <div class="flex items-start gap-6">
                    <div class="w-64">
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">SHA Contribution Rate</label>
                        <div class="relative">
                            <input type="number" name="sha_rate"
                                   value="{{ old('sha_rate', (float)$settings->sha_rate) }}"
                                   step="0.01" min="0" max="100"
                                   class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('sha_rate') border-red-300 @enderror"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                        </div>
                        @error('sha_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1">Employee contribution only (default: 2.75%)</p>
                    </div>
                    <div class="flex-1 bg-emerald-50 rounded-xl px-4 py-3 text-xs text-emerald-700">
                        <strong>Example:</strong> KES 50,000 gross &times; {{ (float)$settings->sha_rate }}% = KES {{ number_format(50000 * (float)$settings->sha_rate / 100, 2) }} SHA
                    </div>
                </div>
            </div>
        </div>

        {{-- NSSF --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">NSSF — National Social Security Fund</h2>
                    <p class="text-xs text-gray-400">Two-tier contribution system. Both employee and employer contribute on the pensionable pay.</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee Contribution Rate</label>
                        <div class="relative">
                            <input type="number" name="nssf_employee_rate"
                                   value="{{ old('nssf_employee_rate', (float)$settings->nssf_employee_rate) }}"
                                   step="0.5" min="0" max="100"
                                   class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('nssf_employee_rate') border-red-300 @enderror"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                        </div>
                        @error('nssf_employee_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employer Contribution Rate</label>
                        <div class="relative">
                            <input type="number" name="nssf_employer_rate"
                                   value="{{ old('nssf_employer_rate', (float)$settings->nssf_employer_rate) }}"
                                   step="0.5" min="0" max="100"
                                   class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('nssf_employer_rate') border-red-300 @enderror"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                        </div>
                        @error('nssf_employer_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Tier I Upper Limit (KES)</label>
                        <input type="number" name="nssf_tier1_limit"
                               value="{{ old('nssf_tier1_limit', (float)$settings->nssf_tier1_limit) }}"
                               step="1" min="1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('nssf_tier1_limit') border-red-300 @enderror"/>
                        @error('nssf_tier1_limit')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1">Rate applies to salary up to this amount</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Tier II Upper Limit (KES)</label>
                        <input type="number" name="nssf_tier2_limit"
                               value="{{ old('nssf_tier2_limit', (float)$settings->nssf_tier2_limit) }}"
                               step="1" min="1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('nssf_tier2_limit') border-red-300 @enderror"/>
                        @error('nssf_tier2_limit')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1">Rate applies to salary between Tier I and this amount</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Housing Levy --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Housing Levy — Affordable Housing Fund</h2>
                    <p class="text-xs text-gray-400">Calculated as a percentage of gross salary. Both employee and employer contribute.</p>
                </div>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee Rate</label>
                        <div class="relative">
                            <input type="number" name="housing_levy_employee_rate"
                                   value="{{ old('housing_levy_employee_rate', (float)$settings->housing_levy_employee_rate) }}"
                                   step="0.1" min="0" max="100"
                                   class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('housing_levy_employee_rate') border-red-300 @enderror"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                        </div>
                        @error('housing_levy_employee_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employer Rate</label>
                        <div class="relative">
                            <input type="number" name="housing_levy_employer_rate"
                                   value="{{ old('housing_levy_employer_rate', (float)$settings->housing_levy_employer_rate) }}"
                                   step="0.1" min="0" max="100"
                                   class="w-full px-3 py-2 pr-8 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('housing_levy_employer_rate') border-red-300 @enderror"/>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                        </div>
                        @error('housing_levy_employer_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Warning + Save --}}
        <div class="flex items-start justify-between gap-6">
            <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 flex-1">
                <p class="text-xs text-amber-700">
                    <strong>Important:</strong> Rate changes only affect payroll processed after saving. Previously generated payslips retain the rates active at the time of processing.
                </p>
            </div>
            <button type="submit"
                    class="shrink-0 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                Save Deduction Rates
            </button>
        </div>

    </form>
</div>
@endsection
