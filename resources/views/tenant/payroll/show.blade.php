@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', $payroll->name . ' Payroll')
@section('page-subtitle', $payroll->start_date->format('M d') . ' - ' . $payroll->end_date->format('M d, Y'))

@section('page-actions')
    <a href="{{ route('tenant.exports.payroll', ['period_id' => $payroll->id]) }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50">Export Excel</a>
    @if($payroll->status === 'draft')
        <button onclick="document.getElementById('approve-modal').style.display='flex'"
                class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
            Approve Payroll
        </button>
    @endif
    <a href="{{ route('tenant.payroll.index') }}" class="text-sm text-gray-500 hover:text-emerald-700">
        &larr; Back to Payroll
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">{{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Employees</p>
            <p class="text-2xl font-semibold text-gray-800">{{ $payroll->records->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Gross Payroll</p>
            <p class="text-2xl font-semibold text-gray-800">KES {{ number_format($payroll->records->sum('gross_salary'), 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Total Deductions</p>
            <p class="text-2xl font-semibold text-red-600">KES {{ number_format($payroll->records->sum('total_deductions'), 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Net Payroll</p>
            <p class="text-2xl font-semibold text-emerald-700">KES {{ number_format($payroll->records->sum('net_salary'), 0) }}</p>
        </div>
    </div>

    {{-- Status & Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div>
                <p class="text-xs text-gray-400">Status</p>
                @php $colors = ['draft' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-emerald-50 text-emerald-600']; @endphp
                <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$payroll->status] ?? '' }} capitalize">{{ $payroll->status }}</span>
            </div>
            @if($payroll->payment_date)
            <div>
                <p class="text-xs text-gray-400">Payment Date</p>
                <p class="text-sm font-medium text-gray-700">{{ $payroll->payment_date->format('M d, Y') }}</p>
            </div>
            @endif
            @if($payroll->payment_mode)
            <div>
                <p class="text-xs text-gray-400">Payment Mode</p>
                <p class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $payroll->payment_mode) }}</p>
            </div>
            @endif
        </div>
        @if($payroll->status === 'approved')
            <a href="{{ route('tenant.payslip.download-all', $payroll) }}"
               class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Download All Payslips
            </a>
        @endif
    </div>

    {{-- Payroll Records --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-semibold text-gray-800">Payroll Records</h2>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Employee</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-3">Basic</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-3">Gross</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-3">PAYE</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-3">NHIF</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-3">NSSF</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-3">Housing</th>
                    <th class="text-right text-xs text-gray-400 font-medium px-6 py-3">Net Pay</th>
                    @if($payroll->status === 'approved')
                        <th class="text-right text-xs text-gray-400 font-medium px-6 py-3">Payslip</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($payroll->records as $record)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3">
                        <p class="text-sm font-medium text-gray-800">{{ $record->employee->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $record->employee->job_title }}</p>
                    </td>
                    <td class="px-6 py-3 text-right text-sm text-gray-600">{{ number_format($record->basic_salary, 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm font-medium text-gray-800">{{ number_format($record->gross_salary, 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-red-500">{{ number_format($record->paye, 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-red-500">{{ number_format($record->nhif, 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-red-500">{{ number_format($record->nssf_employee, 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-red-500">{{ number_format($record->housing_levy, 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm font-semibold text-emerald-700">{{ number_format($record->net_salary, 0) }}</td>
                    @if($payroll->status === 'approved')
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('tenant.payslip.download', [$payroll, $record]) }}"
                               class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Download</a>
                        </td>
                    @endif
                </tr>
                @endforeach
                {{-- Totals --}}
                <tr class="bg-gray-50 font-semibold border-t-2 border-gray-200">
                    <td class="px-6 py-3 text-sm text-gray-800">TOTALS</td>
                    <td class="px-6 py-3 text-right text-sm text-gray-800">{{ number_format($payroll->records->sum('basic_salary'), 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-gray-800">{{ number_format($payroll->records->sum('gross_salary'), 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-red-600">{{ number_format($payroll->records->sum('paye'), 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-red-600">{{ number_format($payroll->records->sum('nhif'), 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-red-600">{{ number_format($payroll->records->sum('nssf_employee'), 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-red-600">{{ number_format($payroll->records->sum('housing_levy'), 0) }}</td>
                    <td class="px-6 py-3 text-right text-sm text-emerald-700">{{ number_format($payroll->records->sum('net_salary'), 0) }}</td>
                    @if($payroll->status === 'approved')<td></td>@endif
                </tr>
            </tbody>
        </table>
    </div>

@endsection

{{-- Approve Modal --}}
<div id="approve-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; padding:32px; max-width:480px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <h3 style="font-size:16px; font-weight:600; color:#111827; margin-bottom:8px;">Approve Payroll</h3>
        <p style="font-size:13px; color:#6b7280; margin-bottom:24px;">Total Net Pay: <strong style="color:#065f46;">KES {{ number_format($payroll->records->sum('net_salary'), 2) }}</strong></p>
        <p style="font-size:12px; font-weight:500; color:#374151; margin-bottom:12px;">Select payment mode:</p>

        @php
            $wallet = \App\Models\Central\FacilityWallet::getOrCreate(tenant('id'));
            $totalNetPay = $payroll->records->sum('net_salary');
            $hasBalance = $wallet->hasSufficientBalance($totalNetPay);
        @endphp

        <form method="POST" action="{{ route('tenant.payroll.approve', $payroll) }}">
            @csrf
            <input type="hidden" name="payment_mode" value="wallet"/>
            <button type="submit" {{ !$hasBalance ? 'disabled' : '' }}
                    style="width:100%; margin-bottom:12px; padding:14px; border-radius:10px; border:2px solid {{ $hasBalance ? '#065f46' : '#e5e7eb' }}; background:{{ $hasBalance ? '#f0fdf4' : '#f9fafb' }}; cursor:{{ $hasBalance ? 'pointer' : 'not-allowed' }}; text-align:left;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <p style="font-size:13px; font-weight:600; color:{{ $hasBalance ? '#065f46' : '#9ca3af' }};">Pay from Wallet</p>
                        <p style="font-size:11px; color:#6b7280; margin-top:2px;">Balance: KES {{ number_format($wallet->balance, 2) }}</p>
                    </div>
                    @if($hasBalance)
                        <span style="font-size:11px; background:#dcfce7; color:#065f46; padding:3px 10px; border-radius:20px;">Sufficient</span>
                    @else
                        <span style="font-size:11px; background:#fee2e2; color:#dc2626; padding:3px 10px; border-radius:20px;">Insufficient</span>
                    @endif
                </div>
            </button>
        </form>

        <form method="POST" action="{{ route('tenant.payroll.approve', $payroll) }}">
            @csrf
            <input type="hidden" name="payment_mode" value="manual"/>
            <button type="submit"
                    style="width:100%; margin-bottom:20px; padding:14px; border-radius:10px; border:2px solid #e5e7eb; background:#f9fafb; cursor:pointer; text-align:left;">
                <div>
                    <p style="font-size:13px; font-weight:600; color:#374151;">Pay Manually</p>
                    <p style="font-size:11px; color:#6b7280; margin-top:2px;">Mark as paid - you handle payments outside the system</p>
                </div>
            </button>
        </form>

        <button onclick="document.getElementById('approve-modal').style.display='none'"
                style="width:100%; padding:10px; border-radius:8px; border:1px solid #e5e7eb; background:white; color:#6b7280; font-size:13px; cursor:pointer;">
            Cancel
        </button>
    </div>
</div>
