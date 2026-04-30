<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $tenantName }}</title>
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

                            <h2 style="margin:0 0 8px;font-size:20px;color:#064e3b;text-align:center;">Welcome to {{ $tenantName }}, {{ $user->name }}!</h2>
                            <p style="margin:0 0 24px;font-size:14px;color:#6b7280;text-align:center;">Your HRIS employee account has been created. Use the details below to set up your password and access your portal.</p>

                            {{-- Account details --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;background:#f0fdf4;border-radius:10px;padding:16px;">
                                <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Email:</strong> {{ $user->email }}</td></tr>
                                <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Role:</strong> {{ ucfirst($user->role) }}</td></tr>
                                <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;"><strong style="color:#064e3b;">Facility:</strong> {{ $tenantName }}</td></tr>
                                @if($temporaryPassword)
                                <tr><td style="font-size:13px;color:#6b7280;padding:4px 0;">
                                    <strong style="color:#064e3b;">Temporary Password:</strong>
                                    <span style="display:inline-block;background:#ecfdf5;border:1px solid #6ee7b7;border-radius:6px;padding:2px 10px;font-family:monospace;font-size:14px;color:#065f46;letter-spacing:0.05em;">{{ $temporaryPassword }}</span>
                                </td></tr>
                                @endif
                            </table>

                            {{-- Instructions --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;border:1px solid #fde68a;background:#fffbeb;border-radius:10px;padding:14px;">
                                <tr>
                                    <td style="font-size:13px;color:#92400e;">
                                        <strong>What to do next:</strong>
                                        <ol style="margin:8px 0 0;padding-left:18px;">
                                            <li style="margin-bottom:4px;">Click the button below to go to the password setup page.</li>
                                            <li style="margin-bottom:4px;">Enter your email and the <strong>Temporary Password</strong> shown above.</li>
                                            <li style="margin-bottom:4px;">Choose a new password (min. 8 characters) and confirm it.</li>
                                            <li>You'll receive a confirmation email with your portal login link.</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $link }}" style="display:inline-block;background-color:#065f46;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:12px 32px;border-radius:8px;">
                                            Set My Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:12px;color:#9ca3af;text-align:center;margin:0 0 8px;">
                                Or copy this link into your browser:<br>
                                <span style="color:#065f46;word-break:break-all;">{{ $link }}</span>
                            </p>

                            <hr style="border:none;border-top:1px solid #d1fae5;margin:20px 0;"/>
                            <p style="font-size:12px;color:#9ca3af;text-align:center;margin:0;">If you did not expect this email, please ignore it or contact your HR department.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-top:24px;">
                            <p style="font-size:11px;color:#9ca3af;margin:0;">&copy; {{ date('Y') }} OpEx Healthcare. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
