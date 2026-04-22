@extends('emails.layout')
@section('content')
<h2 style="color:#064e3b;margin:0 0 16px;">Expense Claim Approved</h2>
<p>Dear {{ $employee->first_name }},</p>
<p>Your expense claim has been <strong>approved</strong>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
    <tr><td style="padding:8px;color:#666;">Claim Number</td><td style="padding:8px;font-weight:bold;">{{ $expense->claim_number }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Title</td><td style="padding:8px;">{{ $expense->title }}</td></tr>
    <tr><td style="padding:8px;color:#666;">Amount</td><td style="padding:8px;font-weight:bold;color:#064e3b;">KES {{ number_format($expense->total_amount, 2) }}</td></tr>
</table>
<p>The reimbursement will be processed shortly. Please contact HR if you have any queries.</p>
@endsection
