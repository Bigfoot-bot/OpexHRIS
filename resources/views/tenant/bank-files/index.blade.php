@extends('tenant.layouts.app')
@section('page-title', 'Bank Files')
@section('page-subtitle', 'Generate salary payment files for bank transfers')
@section('content')
<div class="grid grid-cols-2 gap-6">

    {{-- Payroll Bank File --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-2">Payroll Bank File</h2>
        <p class="text-xs text-gray-400 mb-4">Generate salary payment file for approved payroll periods</p>
        @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('tenant.bank-files.generate') }}" class="space-y-4">
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
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Bank Format *</label>
                <select name="format" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="generic">Generic CSV</option>
                    <option value="kcb">KCB Bank</option>
                    <option value="equity">Equity Bank</option>
                    <option value="cooperative">Co-operative Bank</option>
                    <option value="standard">Standard Chartered</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Download Bank File
            </button>
        </form>
    </div>

    {{-- Expense Bank File --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-2">Expense Payments File</h2>
        <p class="text-xs text-gray-400 mb-4">Generate payment file for approved expense claims</p>
        <form method="POST" action="{{ route('tenant.bank-files.expense') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Month *</label>
                <input type="month" name="month" required value="{{ date('Y-m') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Download Expense File
            </button>
        </form>
    </div>

    {{-- Info Cards --}}
    <div class="col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Supported Bank Formats</h2>
        <div class="grid grid-cols-4 gap-4">
            <div class="border border-gray-100 rounded-xl p-4">
                <p class="text-sm font-semibold text-gray-800">KCB Bank</p>
                <p class="text-xs text-gray-400 mt-1">Account No, Name, Amount, Narration, Currency</p>
            </div>
            <div class="border border-gray-100 rounded-xl p-4">
                <p class="text-sm font-semibold text-gray-800">Equity Bank</p>
                <p class="text-xs text-gray-400 mt-1">Beneficiary Name, Account, Bank, Branch, Amount</p>
            </div>
            <div class="border border-gray-100 rounded-xl p-4">
                <p class="text-sm font-semibold text-gray-800">Co-operative Bank</p>
                <p class="text-xs text-gray-400 mt-1">Employee, Account, Bank Code, Branch Code, Amount</p>
            </div>
            <div class="border border-gray-100 rounded-xl p-4">
                <p class="text-sm font-semibold text-gray-800">Generic CSV</p>
                <p class="text-xs text-gray-400 mt-1">All fields included for any bank</p>
            </div>
        </div>
    </div>
</div>
@endsection
