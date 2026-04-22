<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $record->employee->full_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }
        .page { padding: 0; }

        /* Header */
        .header { background: #064e3b; color: white; padding: 24px 30px; }
        .header-inner { display: flex; justify-content: space-between; align-items: center; }
        .header-left { display: flex; align-items: center; gap: 14px; }
        .header-logo { height: 56px; width: 56px; object-fit: contain; background: white; border-radius: 8px; padding: 4px; }
        .header-title h1 { font-size: 20px; font-weight: bold; letter-spacing: 0.02em; }
        .header-title p { font-size: 10px; opacity: 0.65; margin-top: 2px; }
        .header-title .address { font-size: 9px; opacity: 0.55; margin-top: 4px; }
        .payslip-badge { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); padding: 8px 16px; border-radius: 6px; text-align: center; }
        .payslip-badge p { font-size: 9px; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.08em; }
        .payslip-badge h2 { font-size: 15px; font-weight: bold; margin-top: 2px; }

        /* Divider */
        .divider { height: 4px; background: linear-gradient(to right, #10b981, #064e3b); }

        /* Body */
        .body { padding: 24px 30px; }
                    <div style="display:flex;gap:10px;">
                        <span class="info-label">Branch Code</span>
                        <span class="info-value">{{ $record->employee->branch_code ?? '-' }}</span>
                    </div>

        /* Info Section */
        .info-grid { display: flex; gap: 20px; margin-bottom: 20px; }
        .info-card { flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .info-card-header { background: #f0fdf4; padding: 8px 14px; border-bottom: 1px solid #e5e7eb; }
        .info-card-header h3 { font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; color: #065f46; font-weight: bold; }
        .info-card-body { padding: 10px 14px; }
        .info-row { display: flex; padding: 3px 0; border-bottom: 1px solid #f9fafb; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; width: 110px; font-size: 10px; flex-shrink: 0; }
        .info-value { font-size: 10px; font-weight: 600; color: #111827; }

        /* Tables */
        .tables-grid { display: flex; gap: 16px; margin-bottom: 16px; }
        .table-card { flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .table-card-header { padding: 8px 14px; border-bottom: 1px solid #e5e7eb; }
        .earnings-header { background: #ecfdf5; }
        .deductions-header { background: #fef2f2; }
        .earnings-header h3 { font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; color: #065f46; font-weight: bold; }
        .deductions-header h3 { font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; color: #991b1b; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f9fafb; padding: 7px 12px; font-size: 9px; text-transform: uppercase; color: #6b7280; text-align: left; letter-spacing: 0.05em; }
        th.right { text-align: right; }
        td { padding: 6px 12px; border-bottom: 1px solid #f3f4f6; font-size: 10px; color: #374151; }
        td.right { text-align: right; font-variant-numeric: tabular-nums; }
        .total-row td { font-weight: bold; background: #f9fafb; border-top: 1px solid #e5e7eb; color: #111827; font-size: 11px; }

        /* Net Pay */
        .net-pay-bar { background: #064e3b; color: white; padding: 14px 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .net-pay-bar .label { font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.08em; }
        .net-pay-bar .amount { font-size: 24px; font-weight: bold; }
        .net-pay-bar .period { font-size: 9px; opacity: 0.55; margin-top: 2px; }

        /* Employer Contributions */
        .employer-box { background: #f0fdf4; border: 1px solid #d1fae5; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 10px; color: #065f46; }
        .employer-box strong { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px; }

        /* Bank Details */
        .bank-section { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 20px; }
        .bank-header { background: #eff6ff; padding: 8px 14px; border-bottom: 1px solid #e5e7eb; }
        .bank-header h3 { font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; color: #1d4ed8; font-weight: bold; }
        .bank-body { padding: 10px 14px; display: flex; gap: 40px; }

        /* Signature */
        .signature-section { display: flex; justify-content: space-between; margin-bottom: 20px; margin-top: 10px; }
        .signature-box { width: 45%; }
        .signature-line { border-top: 1px solid #374151; margin-top: 40px; padding-top: 6px; }
        .signature-line p { font-size: 9px; color: #6b7280; }

        /* Footer */
        .footer { border-top: 1px solid #e5e7eb; padding-top: 12px; text-align: center; font-size: 9px; color: #9ca3af; }
        .footer strong { color: #6b7280; }

        /* Confidential Banner */
        .confidential { text-align: center; font-size: 8px; color: #d1d5db; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 6px; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo" class="header-logo"/>
                @endif
                <div class="header-title">
                    <h1>{{ tenant('name') }}</h1>
                    <p>Employee Payslip</p>
                    @if(!empty($tenant->address))
                        <p class="address">{{ $tenant->address }}@if(!empty($tenant->county)), {{ $tenant->county }}@endif</p>
                    @endif
                    @if(!empty($tenant->phone))
                        <p class="address">Tel: {{ $tenant->phone }}</p>
                    @endif
                </div>
            </div>
            <div class="payslip-badge">
                <p>Pay Period</p>
                <h2>{{ $payroll->name }}</h2>
                @if($payroll->payment_date)
                    <p style="font-size:9px;opacity:0.6;margin-top:4px;">Pay Date: {{ $payroll->payment_date->format('M d, Y') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="body">

        {{-- Employee & Period Info --}}
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-header"><h3>Employee Details</h3></div>
                <div class="info-card-body">
                    <div class="info-row">
                        <span class="info-label">Full Name</span>
                        <span class="info-value">{{ $record->employee->full_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Employee No.</span>
                        <span class="info-value">{{ $record->employee->employee_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Job Title</span>
                        <span class="info-value">{{ $record->employee->job_title ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Department</span>
                        <span class="info-value">{{ $record->employee->department ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Employment Type</span>
                        <span class="info-value" style="text-transform:capitalize;">{{ $record->employee->employment_type ?? '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="info-card">
                <div class="info-card-header"><h3>Tax & Compliance</h3></div>
                <div class="info-card-body">
                    <div class="info-row">
                        <span class="info-label">KRA PIN</span>
                        <span class="info-value">{{ $record->employee->kra_pin ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">NHIF No.</span>
                        <span class="info-value">{{ $record->employee->nhif_number ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">NSSF No.</span>
                        <span class="info-value">{{ $record->employee->nssf_number ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Period Start</span>
                        <span class="info-value">{{ $payroll->start_date->format('M d, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Period End</span>
                        <span class="info-value">{{ $payroll->end_date->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Earnings & Deductions --}}
        <div class="tables-grid">
            <div class="table-card">
                <div class="table-card-header earnings-header"><h3>Earnings</h3></div>
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="right">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Basic Salary</td><td class="right">{{ number_format($record->basic_salary, 2) }}</td></tr>
                        @if($record->house_allowance > 0)
                        <tr><td>House Allowance</td><td class="right">{{ number_format($record->house_allowance, 2) }}</td></tr>
                        @endif
                        @if($record->transport_allowance > 0)
                        <tr><td>Transport Allowance</td><td class="right">{{ number_format($record->transport_allowance, 2) }}</td></tr>
                        @endif
                        @if($record->medical_allowance > 0)
                        <tr><td>Medical Allowance</td><td class="right">{{ number_format($record->medical_allowance, 2) }}</td></tr>
                        @endif
                        @if($record->other_allowances > 0)
                        <tr><td>Other Allowances</td><td class="right">{{ number_format($record->other_allowances, 2) }}</td></tr>
                        @endif
                        @if($record->overtime_pay > 0)
                        <tr><td>Overtime Pay</td><td class="right">{{ number_format($record->overtime_pay, 2) }}</td></tr>
                        @endif
                        <tr class="total-row"><td>Gross Salary</td><td class="right">{{ number_format($record->gross_salary, 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="table-card">
                <div class="table-card-header deductions-header"><h3>Deductions</h3></div>
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="right">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>PAYE</td><td class="right">{{ number_format($record->paye, 2) }}</td></tr>
                        <tr><td>NHIF / SHIF</td><td class="right">{{ number_format($record->nhif, 2) }}</td></tr>
                        <tr><td>NSSF (Employee)</td><td class="right">{{ number_format($record->nssf_employee, 2) }}</td></tr>
                        <tr><td>Housing Levy</td><td class="right">{{ number_format($record->housing_levy, 2) }}</td></tr>
                        @if($record->loan_deduction > 0)
                        <tr><td>Loan Deduction</td><td class="right">{{ number_format($record->loan_deduction, 2) }}</td></tr>
                        @endif
                        @if($record->other_deductions > 0)
                        <tr><td>Other Deductions</td><td class="right">{{ number_format($record->other_deductions, 2) }}</td></tr>
                        @endif
                        <tr class="total-row"><td>Total Deductions</td><td class="right">{{ number_format($record->total_deductions, 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Net Pay --}}
        <div class="net-pay-bar">
            <div>
                <p class="label">Net Pay</p>
                <p class="period">{{ $payroll->name }}</p>
            </div>
            <div class="amount">KES {{ number_format($record->net_salary, 2) }}</div>
        </div>

        {{-- Employer Contributions --}}
        <div class="employer-box">
            <strong>Employer Contributions</strong>
            NSSF (Employer): KES {{ number_format($record->nssf_employer, 2) }}
        </div>

        {{-- Bank Details --}}
        <div class="bank-section">
            <div class="bank-header"><h3>Bank / Payment Details</h3></div>
            <div class="bank-body">
                <div class="info-row" style="flex-direction:column;gap:6px;">
                    <div style="display:flex;gap:10px;">
                        <span class="info-label">Bank Name</span>
                        <span class="info-value">{{ $record->employee->bank_name ?? '-' }}</span>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <span class="info-label">Account No.</span>
                        <span class="info-value">{{ $record->employee->bank_account_number ?? '-' }}</span>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <span class="info-label">Branch</span>
                        <span class="info-value">{{ $record->employee->bank_branch ?? '-' }}</span>
                    </div>
                </div>
                <div class="info-row" style="flex-direction:column;gap:6px;">
                    <div style="display:flex;gap:10px;">
                        <span class="info-label">Bank Code</span>
                        <span class="info-value">{{ $record->employee->bank_code ?? '-' }}</span>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <span class="info-label">Currency</span>
                        <span class="info-value">KES</span>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <span class="info-label">Pay Date</span>
                        <span class="info-value">{{ $payroll->payment_date ? $payroll->payment_date->format('M d, Y') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Signature Lines --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <p>Prepared By</p>
                    <p style="margin-top:2px;font-weight:600;">{{ tenant('name') }} HR Department</p>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <p>Employee Acknowledgement</p>
                    <p style="margin-top:2px;font-weight:600;">{{ $record->employee->full_name }}</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="confidential">Confidential — For Addressee Only</div>
        <div class="footer">
            <p>This is a computer-generated payslip. <strong>{{ tenant('name') }}</strong> &middot; Generated on {{ now()->format('M d, Y \a\t H:i') }}</p>
        </div>

    </div>
</div>
</body>
</html>


