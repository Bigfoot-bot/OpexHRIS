<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>New Announcement</title></head>
<body style="margin:0;padding:0;background-color:#f0fdf4;font-family:'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;padding:40px 20px;">
        <tr><td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
                <tr><td align="center" style="padding-bottom:24px;">
                    <p style="margin:12px 0 0;font-size:18px;font-weight:600;color:#064e3b;">OpEx HRIS</p>
                    <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">Healthcare HR Management Platform</p>
                </td></tr>
                <tr><td style="background:#ffffff;border-radius:16px;border:1px solid #d1fae5;padding:40px;">
                    <h2 style="margin:0 0 8px;font-size:20px;color:#064e3b;text-align:center;">New Announcement</h2>
                    <p style="margin:0 0 24px;font-size:14px;color:#6b7280;text-align:center;">Hi {{ $recipient }}, you have a new announcement from {{ $sender }}.</p>
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;background:#f0fdf4;border-radius:10px;padding:16px;">
                        <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Title:</strong> {{ $announcement->title }}</td></tr>
                        <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">From:</strong> {{ $sender }}</td></tr>
                        <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Date:</strong> {{ ($announcement->created_at ?? now())->format('M d, Y h:i A') }}</td></tr>
                        <tr><td style="font-size:13px;color:#6b7280;padding:8px 0 0;"><strong style="color:#064e3b;">Message:</strong><br/><span style="color:#374151;">{{ $announcement->body }}</span></td></tr>
                    </table>
                    @if($announcement->meeting_link)
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                        <tr><td align="center">
                            <a href="{{ $announcement->meeting_link }}"
                               style="display:inline-block;background-color:#2563eb;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:12px 32px;border-radius:8px;">
                                Join Meeting
                            </a>
                        </td></tr>
                    </table>
                    @endif
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                        <tr><td align="center">
                            <a href="{{ $link }}" style="display:inline-block;background-color:#065f46;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:12px 32px;border-radius:8px;">View Announcement</a>
                        </td></tr>
                    </table>
                    <hr style="border:none;border-top:1px solid #d1fae5;margin:0 0 24px;"/>
                    <p style="font-size:12px;color:#9ca3af;text-align:center;margin:0;">This is an automated notification from OpEx HRIS.</p>
                </td></tr>
                <tr><td align="center" style="padding-top:24px;">
                    <p style="font-size:11px;color:#9ca3af;margin:0;">� {{ date('Y') }} OpEx Healthcare. All rights reserved.</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
