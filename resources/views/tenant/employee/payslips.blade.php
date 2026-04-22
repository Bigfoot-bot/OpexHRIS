@extends('tenant.employee.layouts.app')

@section('page-title', 'My Payslips')
@section('page-subtitle', 'Your salary and payroll history')

@section('content')

    <div class="bg-white rounded-xl border border-blue-100">
        @if($payslips->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No payslips available yet.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Period</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Basic</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Gross</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">PAYE</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">NHIF</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">NSSF</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Net Pay</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payslips as $payslip)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $payslip->payrollPeriod->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ number_format($payslip->basic_salary) }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ number_format($payslip->gross_salary) }}</td>
                        <td class="px-6 py-3 text-sm text-red-500">{{ number_format($payslip->paye) }}</td>
                        <td class="px-6 py-3 text-sm text-red-500">{{ number_format($payslip->nhif) }}</td>
                        <td class="px-6 py-3 text-sm text-red-500">{{ number_format($payslip->nssf_employee) }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-emerald-900">KES {{ number_format($payslip->net_salary) }}</td>
                        <td class="px-6 py-3">
                            @if($payslip->payrollPeriod->status === 'approved')
                                <a href="{{ route('tenant.payslip.download', [$payslip->payrollPeriod, $payslip]) }}"
                                   class="text-xs text-blue-600 hover:text-blue-800">
                                    Download PDF
                                </a>
                            @else
                                <span class="text-xs text-gray-400">Not approved</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($payslips->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $payslips->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection