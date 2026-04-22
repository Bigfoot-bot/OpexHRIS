@extends('emails.layout')
@section('content')
<h2 style="color:#064e3b;margin:0 0 16px;">MFA Verification Code</h2>
<p>Dear {{ $user->name }},</p>
<p>Your verification code for <strong>{{ tenant('name') }}</strong> HRIS is:</p>
<div style="text-align:center;margin:24px 0;">
    <div style="background:#f0fdf4;border:2px solid #064e3b;border-radius:12px;padding:20px;display:inline-block;">
        <span style="font-size:36px;font-weight:bold;letter-spacing:8px;color:#064e3b;">{{ $code }}</span>
    </div>
</div>
<p>This code expires in <strong>10 minutes</strong>.</p>
<p>If you did not attempt to log in, please contact your administrator immediately.</p>
@endsection
