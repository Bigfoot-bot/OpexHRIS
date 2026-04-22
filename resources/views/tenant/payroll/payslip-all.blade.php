<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll — {{ $payroll->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .container { padding: 30px; }
        .page-break { page-break-after: always; }

        /* Header */
        .header { background: #064e3b; color: white; padding: 20px 30px; margin: -30px -30px 30px -30px; }
        .header h1 { font-size: 22px; font-weight: bold; }
        .header p { font-size: 11px; opacity: 0.7; margin-top: 3px; }
        .header-row { display: flex; justify-content: space-between; align-items: center; }
        .payslip-label { background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 4px; font-size: 11px; }

        /* Employee Info */
        .info-section { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .info-box { width: 48%; }
        .info-box h3 { font-size: 10px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        .info-row { display: flex; margin-bottom: 4px; }
        .info-label { color: #6b7280; width: 120px; font-size: 11px; }
        .info-value { font-size: 11px; font-weight: 500; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; color: #6b7280; }
        td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .amount { text-align: right; }
        .total-row td { font-weight: bold; background: #f9fafb; border-top: 2px solid #e5e7eb; }

        /* Net Pay */
        .net-pay { background: #064e3b; color: white; padding: 15px 20px; border-radius: 8px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center; }
        .net-pay h3 { font-size: 12px; opacity: 0.8; }
        .net-pay .amount { font-size: 22px; font-weight: bold; }

        /* Footer */
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }

        .two-col { display: flex; gap: 20px; }
        .two-col > div { flex: 1; }
    </style>
</head>
<body>

@foreach($payroll->records as $index => $record)
<div class="container {{ !$loop->last ? 'page-break' : '' }}">

    {{-- Header --}}
    <div class="header">
        <div class="header-row">
            <div>
                <h1>{{ tenant('name') }}</h1>
                <p>Employee Payslip</p>
            </div>
            <div class="payslip-label">{{ $payroll->name }}</div>
        </div>
    </div>

    {{-- Employee & Period Info --}}
    <div class="info-section">
        <div class="info-box">
            <h3>Employee Details</h3>
            <div class="info-row">
                <span class="info-label">Name</span>
                <span class="info-value">{{ $record->employee->full_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Employee No.</span>
                <span class="info-value">{{ $record->employee->employee_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Department</span>
                <span class="info-value">{{ $record->employee->department ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Job Title</span>
                <span class="info-value">{{ $record->employee->job_title ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">KRA PIN</span>
                <span class="info-value">{{ $record->employee->kra_pin ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NHIF No.</span>
                <span class="info-value">{{ $record->employee->nhif_number ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NSSF No.</span>
                <span class="info-value">{{ $record->employee->nssf_number ?? '—' }}</span>
            </div>
        </div>
        <div class="info-box">
            <h3>Payroll Period</h3>
            <div class="info-row">
                <span class="info-label">Period</span>
                <span class="info-value">{{ $payroll->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Start Date</span>
                <span class="info-value">{{ $payroll->start_date->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">End Date</span>
                <span class="info-value">{{ $payroll->end_date->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Date</span>
                <span class="info-value">{{ $payroll->payment_date ? $payroll->payment_date->format('M d, Y') : '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value" style="text-transform: capitalize;">{{ $payroll->status }}</span>
            </div>
        </div>
    </div>

    {{-- Earnings & Deductions --}}
    <div class="two-col">
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Earnings</th>
                        <th class="amount">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Salary</td>
                        <td class="amount">{{ number_format($record->basic_salary, 2) }}</td>
                    </tr>
                    @if($record->house_allowance > 0)
                    <tr>
                        <td>House Allowance</td>
                        <td class="amount">{{ number_format($record->house_allowance, 2) }}</td>
                    </tr>
                    @endif
                    @if($record->transport_allowance > 0)
                    <tr>
                        <td>Transport Allowance</td>
                        <td class="amount">{{ number_format($record->transport_allowance, 2) }}</td>
                    </tr>
                    @endif
                    @if($record->medical_allowance > 0)
                    <tr>
                        <td>Medical Allowance</td>
                        <td class="amount">{{ number_format($record->medical_allowance, 2) }}</td>
                    </tr>
                    @endif
                    @if($record->other_allowances > 0)
                    <tr>
                        <td>Other Allowances</td>
                        <td class="amount">{{ number_format($record->other_allowances, 2) }}</td>
                    </tr>
                    @endif
                    @if($record->overtime_pay > 0)
                    <tr>
                        <td>Overtime Pay</td>
                        <td class="amount">{{ number_format($record->overtime_pay, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Gross Salary</td>
                        <td class="amount">{{ number_format($record->gross_salary, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Deductions</th>
                        <th class="amount">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PAYE</td>
                        <td class="amount">{{ number_format($record->paye, 2) }}</td>
                    </tr>
                    <tr>
                        <td>NHIF</td>
                        <td class="amount">{{ number_format($record->nhif, 2) }}</td>
                    </tr>
                    <tr>
                        <td>NSSF (Employee)</td>
                        <td class="amount">{{ number_format($record->nssf_employee, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Housing Levy</td>
                        <td class="amount">{{ number_format($record->housing_levy, 2) }}</td>
                    </tr>
                    @if($record->loan_deduction > 0)
                    <tr>
                        <td>Loan Deduction</td>
                        <td class="amount">{{ number_format($record->loan_deduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($record->other_deductions > 0)
                    <tr>
                        <td>Other Deductions</td>
                        <td class="amount">{{ number_format($record->other_deductions, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Total Deductions</td>
                        <td class="amount">{{ number_format($record->total_deductions, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Net Pay --}}
    <div class="net-pay">
        <div>
            <h3>NET PAY</h3>
            <p style="font-size: 10px; opacity: 0.6; margin-top: 2px;">{{ $payroll->name }}</p>
        </div>
        <div class="amount">KES {{ number_format($record->net_salary, 2) }}</div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>This is a computer-generated payslip and does not require a signature.</p>
        <p style="margin-top: 3px;">{{ tenant('name') }} · Generated on {{ now()->format('M d, Y H:i') }}</p>
    </div>

</div>
@endforeach

</body>
</html>