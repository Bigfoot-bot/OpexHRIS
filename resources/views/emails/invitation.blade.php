<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Invited!</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #065f46; padding: 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: rgba(255,255,255,0.7); margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .body h2 { color: #065f46; font-size: 18px; margin-top: 0; }
        .body p { color: #555; line-height: 1.6; font-size: 14px; }
        .btn { display: inline-block; background: #065f46; color: #fff !important; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: bold; margin: 16px 0; }
        .info-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .info-box p { margin: 4px 0; color: #065f46; font-size: 13px; }
        .footer { background: #f9f9f9; padding: 20px 32px; text-align: center; border-top: 1px solid #eee; }
        .footer p { color: #999; font-size: 12px; margin: 0; }
        .link-text { word-break: break-all; color: #065f46; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 OpEx HRIS</h1>
            <p>Healthcare HR Management Platform</p>
        </div>
        <div class="body">
            <h2>You've been invited!</h2>
            <p>Hello <strong>{{ $invitation->name }}</strong>,</p>
            <p>You have been invited to join the <strong>HRIS Portal</strong> as a <strong>{{ $invitation->role }}</strong>. Click the button below to set up your account.</p>

            <div class="info-box">
                <p><strong>📧 Email:</strong> {{ $invitation->email }}</p>
                <p><strong>👤 Role:</strong> {{ $invitation->role }}</p>
                <p><strong>⏰ Expires:</strong> {{ $invitation->expires_at->format('M d, Y \a\t H:i') }}</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ $link }}" class="btn">Set Up My Account →</a>
            </div>

            <p style="font-size: 13px; color: #888;">If the button doesn't work, copy and paste this link into your browser:</p>
            <p class="link-text">{{ $link }}</p>
        </div>
        <div class="footer">
            <p>This invitation expires in 7 days. If you did not expect this invitation, please ignore this email.</p>
        </div>
    </div>
</body>
</html>