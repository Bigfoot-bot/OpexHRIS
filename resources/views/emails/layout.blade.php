<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? "OpEx HRIS Notification" }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f0fdf4;font-family:'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background-color:#065f46;border-radius:12px;width:48px;height:48px;text-align:center;vertical-align:middle;">
                                        <div style="width:24px;height:24px;background:#fff;border-radius:6px;margin:12px;"></div>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:12px 0 0;font-size:18px;font-weight:600;color:#064e3b;">OpEx HRIS</p>
                            <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">Healthcare HR Management Platform</p>
                        </td>
                    </tr>
                    <!-- Card -->
                    <tr>
                        <td style="background:#ffffff;border-radius:16px;border:1px solid #d1fae5;padding:40px;">
                            {{ $slot }}
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top:24px;">
                            <p style="font-size:11px;color:#9ca3af;margin:0;">© {{ date('Y') }} OpEx Healthcare. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
