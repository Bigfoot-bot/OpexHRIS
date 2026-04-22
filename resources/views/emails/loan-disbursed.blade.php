@extends('emails.layout')
@section('content')
<h2 style="color:#064e3b;margin:0 0 16px;">Loan Disbursed</h2>
<p>Dear {{ $employee->first_name }},</p>
<p>Your loan has been <strong>disbursed</strong> to your bank account.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
    <tr><td style="padding:8px;color:#666;">Loan Number</td><td style="padding:8px;font-weight:bold;">{{ $loan->loan_number }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Amount Disbursed</td><td style="padding:8px;font-weight:bold;color:#064e3b;">KES {{ number_format($loan->amount, 2) }}</td></tr>
    <tr><td style="padding:8px;color:#666;">Bank</td><td style="padding:8px;">{{ $bankName }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Account Number</td><td style="padding:8px;">{{ $accountNumber }}</td></tr>
    <tr><td style="padding:8px;color:#666;">First Repayment</td><td style="padding:8px;">{{ $loan->start_repayment_date?->format('M d, Y') }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Monthly Deduction</td><td style="padding:8px;">KES {{ number_format($loan->monthly_deduction, 2) }}</td></tr>
</table>
<p>Repayments will be automatically deducted from your monthly salary. Please ensure your account is in good standing.</p>
@endsection
