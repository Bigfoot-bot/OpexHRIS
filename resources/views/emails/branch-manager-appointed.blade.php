<x-emails.layout>
    <p style="margin:0 0 8px;font-size:20px;font-weight:700;color:#064e3b;">Branch Manager Appointment</p>
    <p style="margin:0 0 24px;font-size:13px;color:#6b7280;">{{ $tenantName }}</p>

    <p style="margin:0 0 16px;font-size:14px;color:#374151;">Dear <strong>{{ $manager->name }}</strong>,</p>

    <p style="margin:0 0 16px;font-size:14px;color:#374151;">
        We are pleased to inform you that you have been appointed as the <strong>Branch Manager</strong> of
        <strong>{{ $branch->name }}</strong>.
    </p>

    <table cellpadding="0" cellspacing="0" width="100%" style="background:#f0fdf4;border-radius:10px;margin:0 0 24px;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 8px;font-size:12px;font-weight:600;color:#065f46;text-transform:uppercase;letter-spacing:0.05em;">Branch Details</p>
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

    <p style="margin:0 0 24px;font-size:14px;color:#374151;">
        You can access the branch portal to manage your team, track attendance, and oversee branch operations.
    </p>

    <p style="margin:0;font-size:13px;color:#6b7280;">
        If you have any questions, please contact your HR administrator.
    </p>
</x-emails.layout>
