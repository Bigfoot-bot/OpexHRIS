<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch Manager Appointment</title>
</head>
<body style="margin:0;padding:0;background-color:#f0fdf4;font-family:'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <p style="margin:0 0 4px;font-size:18px;font-weight:700;color:#064e3b;">OpEx HRIS</p>
                            <p style="margin:0;font-size:12px;color:#6b7280;">{{ $tenantName }}</p>
                        </td>
                    </tr>
                    <!-- Card -->
                    <tr>
                        <td style="background:#ffffff;border-radius:16px;border:1px solid #d1fae5;padding:40px;">
                            <p style="margin:0 0 6px;font-size:20px;font-weight:700;color:#064e3b;">Branch Manager Appointment</p>
                            <p style="margin:0 0 24px;font-size:14px;color:#374151;">Dear <strong>{{ $manager->name }}</strong>,</p>
                            <p style="margin:0 0 20px;font-size:14px;color:#374151;">
                                We are pleased to inform you that you have been appointed as the
                                <strong>Branch Manager</strong> of <strong>{{ $branch->name }}</strong>.
                            </p>

                            <!-- Branch details box -->
                            <table cellpadding="0" cellspacing="0" width="100%" style="background:#f0fdf4;border-radius:10px;margin:0 0 24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="margin:0 0 10px;font-size:11px;font-weight:600;color:#065f46;text-transform:uppercase;letter-spacing:0.05em;">Branch Details</p>
                                        <p style="margin:0 0 4px;font-size:13px;color:#374151;"><strong>Branch:</strong> {{ $branch->name }}</p>
                                        @if($branch->address)
                                        <p style="margin:0 0 4px;font-size:13px;color:#374151;"><strong>Address:</strong> {{ $branch->address }}</p>
                                        @endif
                                        @if($branch->phone)
                                        <p style="margin:0 0 4px;font-size:13px;color:#374151;"><strong>Phone:</strong> {{ $branch->phone }}</p>
                                        @endif
                                        <p style="margin:0;font-size:13px;color:#374151;"><strong>Status:</strong> {{ ucfirst($branch->status) }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px;font-size:14px;color:#374151;">
                                You can log in to the branch portal to manage your team and oversee branch operations.
                            </p>
                            <p style="margin:0;font-size:13px;color:#6b7280;">
                                If you have any questions, please contact your HR administrator.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
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
