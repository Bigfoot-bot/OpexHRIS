@extends('emails.layout')
@section('content')
<h2 style="color:#064e3b;margin:0 0 16px;">New Expense Claim</h2>
<p>Dear {{ $admin->name }},</p>
<p>A new expense claim has been submitted and requires your review.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
    <tr><td style="padding:8px;color:#666;">Employee</td><td style="padding:8px;font-weight:bold;">{{ $employee->first_name }} {{ $employee->last_name }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Claim Number</td><td style="padding:8px;">{{ $expense->claim_number }}</td></tr>
    <tr><td style="padding:8px;color:#666;">Title</td><td style="padding:8px;">{{ $expense->title }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Amount</td><td style="padding:8px;font-weight:bold;">KES {{ number_format($expense->total_amount, 2) }}</td></tr>
</table>
<p>Please log in to the HRIS portal to review and approve or reject this claim.</p>
@endsection
