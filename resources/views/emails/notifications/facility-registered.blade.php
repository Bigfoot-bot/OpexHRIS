<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>New Facility Registered</title></head>
<body style="margin:0;padding:0;background-color:#f0fdf4;font-family:'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;padding:40px 20px;">
        <tr><td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
                <tr><td align="center" style="padding-bottom:24px;">
                    <p style="margin:12px 0 0;font-size:18px;font-weight:600;color:#064e3b;">OpEx HRIS</p>
                    <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">Healthcare HR Management Platform</p>
                </td></tr>
                <tr><td style="background:#ffffff;border-radius:16px;border:1px solid #d1fae5;padding:40px;">
                    <h2 style="margin:0 0 8px;font-size:20px;color:#064e3b;text-align:center;">New Facility Registered</h2>
                    <p style="margin:0 0 24px;font-size:14px;color:#6b7280;text-align:center;">A new facility has just been onboarded to OpEx HRIS.</p>
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;background:#f0fdf4;border-radius:10px;padding:16px;">
                        <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Facility Name:</strong> {{ $tenant->name }}</td></tr>
                        <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Email:</strong> {{ $tenant->email }}</td></tr>
                        <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Plan:</strong> {{ ucfirst($tenant->subscription_plan) }}</td></tr>
                        <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Registered:</strong> {{ $tenant->created_at->format('M d, Y h:i A') }}</td></tr>
                    </table>
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                        <tr><td align="center">
                            <a href="{{ url('/admin/tenants') }}" style="display:inline-block;background-color:#065f46;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:12px 32px;border-radius:8px;">View Facility</a>
                        </td></tr>
                    </table>
                    <hr style="border:none;border-top:1px solid #d1fae5;margin:0 0 24px;"/>
                    <p style="font-size:12px;color:#9ca3af;text-align:center;margin:0;">This is an automated notification from OpEx HRIS.</p>
                </td></tr>
                <tr><td align="center" style="padding-top:24px;">
                    <p style="font-size:11px;color:#9ca3af;margin:0;">© {{ date('Y') }} OpEx Healthcare. All rights reserved.</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
