@extends('tenant.layouts.app')
@section('page-title', 'Statutory Returns')
@section('page-subtitle', 'Generate KRA, NHIF, NSSF and Housing Levy returns')
@section('content')
<div class="grid grid-cols-2 gap-6">

    {{-- P9 Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <span class="text-blue-700 font-bold text-sm">P9</span>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">P9 Annual Tax Card</h2>
                <p class="text-xs text-gray-400">Annual tax deduction card per employee</p>
            </div>
        </div>
        <form method="POST" action="{{ route('tenant.statutory.p9') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Year *</label>
                <select name="year" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Download P9</button>
        </form>
    </div>

    {{-- P10 Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                <span class="text-purple-700 font-bold text-sm">P10</span>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">P10 Monthly PAYE Return</h2>
                <p class="text-xs text-gray-400">Monthly PAYE tax return for KRA iTax</p>
            </div>
        </div>
        <form method="POST" action="{{ route('tenant.statutory.p10') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Payroll Period *</label>
                <select name="period_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select Period</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="width:100%;background-color:#7c3aed;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Download P10</button>
        </form>
    </div>

    {{-- NHIF Return --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                <span class="text-emerald-700 font-bold text-xs">NHIF</span>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">NHIF Monthly Return</h2>
                <p class="text-xs text-gray-400">Monthly NHIF contributions return</p>
            </div>
        </div>
        <form method="POST" action="{{ route('tenant.statutory.nhif') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Payroll Period *</label>
                <select name="period_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select Period</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Download NHIF Return</button>
        </form>
    </div>

    {{-- NSSF Return --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                <span class="text-amber-700 font-bold text-xs">NSSF</span>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">NSSF Monthly Return</h2>
                <p class="text-xs text-gray-400">Monthly NSSF contributions return</p>
            </div>
        </div>
        <form method="POST" action="{{ route('tenant.statutory.nssf') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Payroll Period *</label>
                <select name="period_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select Period</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="width:100%;background-color:#d97706;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Download NSSF Return</button>
        </form>
    </div>

    {{-- Housing Levy --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 col-span-2">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                <span class="text-red-700 font-bold text-xs">AHL</span>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Affordable Housing Levy Return</h2>
                <p class="text-xs text-gray-400">Monthly Housing Levy contributions (Employee 1.5% + Employer 1.5%)</p>
            </div>
        </div>
        <form method="POST" action="{{ route('tenant.statutory.housing-levy') }}" class="flex gap-4">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Payroll Period *</label>
                <select name="period_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select Period</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" style="background-color:#dc2626;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1.5rem;border-radius:0.5rem;border:none;cursor:pointer;">Download Housing Levy Return</button>
            </div>
        </form>
    </div>
</div>
@endsection



