<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Changed</title>
</head>
<body style="margin:0;padding:0;background-color:#f0fdf4;font-family:'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <p style="margin:12px 0 0;font-size:18px;font-weight:600;color:#064e3b;">OpEx HRIS</p>
                            <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">Healthcare HR Management Platform</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#ffffff;border-radius:16px;border:1px solid #d1fae5;padding:40px;">

                            <div style="text-align:center;margin-bottom:24px;">
                                <div style="display:inline-block;width:56px;height:56px;background:#d1fae5;border-radius:50%;line-height:56px;font-size:28px;">&#128274;</div>
                            </div>

                            <h2 style="margin:0 0 8px;font-size:20px;color:#064e3b;text-align:center;">Password Changed Successfully</h2>
                            <p style="margin:0 0 24px;font-size:14px;color:#6b7280;text-align:center;">
                                Hi {{ $admin->name }}, your super admin password was just updated.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;background:#f0fdf4;border-radius:10px;padding:16px;">
                                <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Account:</strong> {{ $admin->email }}</td></tr>
                                <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Changed at:</strong> {{ now()->format('D, d M Y \a\t H:i T') }}</td></tr>
                            </table>

                            <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:24px;">
                                <p style="margin:0;font-size:13px;color:#92400e;">
                                    <strong>Not you?</strong> If you did not make this change, your account may be compromised. Contact your system administrator immediately.
                                </p>
                            </div>

                            <hr style="border:none;border-top:1px solid #d1fae5;margin:20px 0;"/>
                            <p style="font-size:12px;color:#9ca3af;text-align:center;margin:0;">
                                &copy; {{ date('Y') }} OpEx Healthcare. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
