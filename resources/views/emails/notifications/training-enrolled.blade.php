<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Training Enrollment Confirmed</title></head>
<body style="margin:0;padding:0;background-color:#f0fdf4;font-family:'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;padding:40px 20px;">
        <tr><td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
                <tr><td align="center" style="padding-bottom:24px;">
                    <p style="margin:12px 0 0;font-size:18px;font-weight:600;color:#064e3b;">OpEx HRIS</p>
                    <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">Healthcare HR Management Platform</p>
                </td></tr>
                <tr><td style="background:#ffffff;border-radius:16px;border:1px solid #d1fae5;padding:40px;">
                    <h2 style="margin:0 0 8px;font-size:20px;color:#064e3b;text-align:center;">Training Enrollment Confirmed</h2>
                    <p style="margin:0 0 24px;font-size:14px;color:#6b7280;text-align:center;">Hi {{ $user->name }}, you have been enrolled in the following training program.</p>

                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;background:#f0fdf4;border-radius:10px;padding:16px;">
                        <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;"><strong style="color:#064e3b;">Training:</strong> {{ $training->title }}</td></tr>
                        <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;"><strong style="color:#064e3b;">Type:</strong> {{ ucfirst($training->type) }}</td></tr>
                        <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;"><strong style="color:#064e3b;">Category:</strong> {{ ucwords(str_replace('_', ' ', $training->category)) }}</td></tr>
                        @if($training->provider)
                        <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;"><strong style="color:#064e3b;">Provider:</strong> {{ $training->provider }}</td></tr>
                        @endif
                        <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;"><strong style="color:#064e3b;">Start Date:</strong> {{ \Carbon\Carbon::parse($training->start_date)->format('M d, Y') }}</td></tr>
                        <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;"><strong style="color:#064e3b;">End Date:</strong> {{ \Carbon\Carbon::parse($training->end_date)->format('M d, Y') }}</td></tr>
                        @if($training->type === 'online')
                        <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;"><strong style="color:#064e3b;">Location:</strong> Online</td></tr>
                        @elseif($training->location)
                        <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;"><strong style="color:#064e3b;">Location:</strong> {{ $training->location }}</td></tr>
                        @endif
                        @if($training->cpd_points)
                        <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;"><strong style="color:#064e3b;">CPD Points:</strong> {{ $training->cpd_points }}</td></tr>
                        @endif
                    </table>

                    {{-- Meeting link button for online trainings --}}
                    @if($training->meeting_link)
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
                        <tr><td align="center">
                            <a href="{{ $training->meeting_link }}"
                               style="display:inline-block;background-color:#2563eb;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:12px 32px;border-radius:8px;">
                                Join Meeting
                            </a>
                        </td></tr>
                    </table>
                    @endif

                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                        <tr><td align="center">
                            <a href="{{ $link }}" style="display:inline-block;background-color:#065f46;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:12px 32px;border-radius:8px;">View My Training</a>
                        </td></tr>
                    </table>

                    <hr style="border:none;border-top:1px solid #d1fae5;margin:0 0 24px;"/>
                    <p style="font-size:12px;color:#9ca3af;text-align:center;margin:0;">This is an automated notification from OpEx HRIS.</p>
                </td></tr>
                <tr><td align="center" style="padding-top:24px;">
                    <p style="font-size:11px;color:#9ca3af;margin:0;">&copy; {{ date('Y') }} OpEx Healthcare. All rights reserved.</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
