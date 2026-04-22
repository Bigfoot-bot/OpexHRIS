@extends('emails.layout')
@section('content')
<h2 style="color:#dc2626;margin:0 0 16px;">Expense Claim Update</h2>
<p>Dear {{ $employee->first_name }},</p>
<p>Your expense claim <strong>{{ $expense->claim_number }}</strong> has not been approved at this time.</p>
@if($expense->rejection_reason)
<div style="background:#fef2f2;padding:12px;border-radius:8px;margin:16px 0;">
    <p style="color:#dc2626;margin:0;font-size:14px;"><strong>Reason:</strong> {{ $expense->rejection_reason }}</p>
</div>
@endif
<p>Please contact the HR department for more information.</p>
@endsection
