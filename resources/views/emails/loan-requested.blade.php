@extends('emails.layout')
@section('content')
<h2 style="color:#064e3b;margin:0 0 16px;">New Loan Application</h2>
<p>Dear {{ $admin->name }},</p>
<p>A new loan application has been submitted and requires your review.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
    <tr><td style="padding:8px;color:#666;">Employee</td><td style="padding:8px;font-weight:bold;">{{ $employee->first_name }} {{ $employee->last_name }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Loan Number</td><td style="padding:8px;">{{ $loan->loan_number }}</td></tr>
    <tr><td style="padding:8px;color:#666;">Amount</td><td style="padding:8px;font-weight:bold;">KES {{ number_format($loan->amount, 2) }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Type</td><td style="padding:8px;text-transform:capitalize;">{{ str_replace('_', ' ', $loan->type) }}</td></tr>
    <tr><td style="padding:8px;color:#666;">Purpose</td><td style="padding:8px;">{{ $loan->purpose }}</td></tr>
</table>
<p>Please log in to the HRIS portal to review and approve or reject this application.</p>
@endsection
