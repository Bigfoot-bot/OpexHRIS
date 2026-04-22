@extends('emails.layout')
@section('content')
<h2 style="color:#dc2626;margin:0 0 16px;">Loan Application Update</h2>
<p>Dear {{ $employee->first_name }},</p>
<p>We regret to inform you that your loan application <strong>{{ $loan->loan_number }}</strong> has not been approved at this time.</p>
<p>Please contact the HR department for more information regarding this decision.</p>
<p>You are welcome to reapply after addressing any outstanding concerns.</p>
@endsection
