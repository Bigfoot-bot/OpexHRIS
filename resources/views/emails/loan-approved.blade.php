@extends('emails.layout')
@section('content')
<h2 style="color:#064e3b;margin:0 0 16px;">Loan Application Approved</h2>
<p>Dear {{ $employee->first_name }},</p>
<p>We are pleased to inform you that your loan application has been <strong>approved</strong>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
    <tr><td style="padding:8px;color:#666;">Loan Number</td><td style="padding:8px;font-weight:bold;">{{ $loan->loan_number }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Amount</td><td style="padding:8px;font-weight:bold;">KES {{ number_format($loan->amount, 2) }}</td></tr>
    <tr><td style="padding:8px;color:#666;">Monthly Deduction</td><td style="padding:8px;">KES {{ number_format($loan->monthly_deduction, 2) }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Repayment Period</td><td style="padding:8px;">{{ $loan->repayment_months }} months</td></tr>
</table>
<p>The loan will be disbursed to your bank account shortly. Monthly deductions will begin from your next payslip.</p>
<p>For any queries, please contact the HR department.</p>
@endsection
