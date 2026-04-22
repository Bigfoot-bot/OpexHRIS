@extends('emails.layout')
@section('content')
<h2 style="color:#064e3b;margin:0 0 16px;">Scheduled Report: {{ $report->name }}</h2>
<p>Dear Team,</p>
<p>Please find attached your scheduled <strong>{{ $report->name }}</strong> report for <strong>{{ $date }}</strong>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
    <tr><td style="padding:8px;color:#666;">Report Type</td><td style="padding:8px;">{{ ucfirst(str_replace('_', ' ', $report->report_type)) }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Frequency</td><td style="padding:8px;">{{ ucfirst($report->frequency) }}</td></tr>
    <tr><td style="padding:8px;color:#666;">Generated</td><td style="padding:8px;">{{ $date }}</td></tr>
    <tr style="background:#f9f9f9;"><td style="padding:8px;color:#666;">Facility</td><td style="padding:8px;">{{ $tenantName }}</td></tr>
</table>
<p>This is an automated report from <strong>{{ $tenantName }}</strong> HRIS. Please do not reply to this email.</p>
@endsection
