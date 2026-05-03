<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        * { margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #ffffff;
            color: #1a1a1a;
        }
        .outer {
            width: 283mm;
            height: 197mm;
            border: 3px solid #064e3b;
            padding: 3.5mm;
            margin: 3mm auto;
        }
        .inner {
            width: 100%;
            height: 190mm;
            border: 1px solid #a7f3d0;
            text-align: center;
            padding: 9mm 16mm 6mm;
        }
        .org-name {
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 3px;
            color: #064e3b;
            text-transform: uppercase;
            margin-bottom: 3mm;
        }
        .divider {
            width: 60mm;
            height: 2px;
            background-color: #064e3b;
            margin: 0 auto 5mm;
        }
        .cert-title {
            font-size: 30pt;
            font-weight: bold;
            color: #064e3b;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 1mm;
        }
        .cert-subtitle {
            font-size: 8.5pt;
            color: #6b7280;
            letter-spacing: 5px;
            text-transform: uppercase;
            margin-bottom: 6mm;
        }
        .presented-to {
            font-size: 8.5pt;
            color: #9ca3af;
            letter-spacing: 1px;
            margin-bottom: 2.5mm;
        }
        .employee-name {
            font-size: 24pt;
            font-weight: bold;
            color: #111827;
            font-style: italic;
            margin-bottom: 5mm;
        }
        .name-underline {
            width: 130mm;
            height: 1.5px;
            background-color: #d1d5db;
            margin: 0 auto 5mm;
        }
        .completed-text {
            font-size: 8.5pt;
            color: #9ca3af;
            margin-bottom: 2mm;
        }
        .training-title {
            font-size: 13pt;
            font-weight: bold;
            color: #064e3b;
            margin-bottom: 4mm;
        }
        .meta-row {
            font-size: 7.5pt;
            color: #6b7280;
            margin-bottom: 7mm;
        }
        .meta-label { font-weight: bold; color: #374151; }
        .meta-sep { color: #d1d5db; margin: 0 2mm; }

        /* Footer table */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4mm;
        }
        .footer-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 8mm;
        }
        .sig-img {
            max-width: 40mm;
            max-height: 14mm;
            display: block;
            margin: 0 auto 2mm;
        }
        .sig-line {
            border-top: 1px solid #9ca3af;
            margin-bottom: 2mm;
        }
        .sig-label {
            font-size: 6.5pt;
            color: #9ca3af;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .sig-name {
            font-size: 7.5pt;
            color: #374151;
            font-weight: bold;
            margin-top: 1mm;
        }
        .sig-title {
            font-size: 6.5pt;
            color: #9ca3af;
            margin-top: 0.5mm;
        }
        .badge-circle {
            width: 22mm;
            height: 22mm;
            border-radius: 50%;
            border: 3px solid #064e3b;
            margin: 0 auto 3mm;
            padding-top: 5.5mm;
        }
        .badge-text {
            font-size: 6.5pt;
            color: #064e3b;
            font-weight: bold;
            letter-spacing: 1px;
            line-height: 1.5;
        }
        .cert-num {
            font-size: 6.5pt;
            color: #9ca3af;
            margin-top: 1mm;
        }
    </style>
</head>
<body>
<div class="outer">
    <div class="inner">

        <div class="org-name">{{ strtoupper($org) }}</div>
        <div class="divider"></div>

        <div class="cert-title">Certificate</div>
        <div class="cert-subtitle">of Completion</div>

        <div class="presented-to">This is to certify that</div>
        <div class="employee-name">{{ $employee->full_name }}</div>
        <div class="name-underline"></div>

        <div class="completed-text">has successfully completed the training program</div>
        <div class="training-title">{{ $training->title }}</div>

        <div class="meta-row">
            <span class="meta-label">Category:</span> {{ ucwords(str_replace('_', ' ', $training->category)) }}
            @if($training->provider)
                <span class="meta-sep">|</span><span class="meta-label">Provider:</span> {{ $training->provider }}
            @endif
            <span class="meta-sep">|</span><span class="meta-label">Date:</span>
            {{ $enrollment->completion_date ? $enrollment->completion_date->format('F d, Y') : $training->end_date->format('F d, Y') }}
            @if($enrollment->cpd_points_earned)
                <span class="meta-sep">|</span><span class="meta-label">CPD Points:</span> {{ $enrollment->cpd_points_earned }}
            @endif
        </div>

        <table class="footer-table">
            <tr>
                <td>
                    @if($sigPath && file_exists($sigPath))
                        <img src="file://{{ $sigPath }}" class="sig-img" alt="Signature"/>
                    @else
                        <div style="height: 14mm;"></div>
                    @endif
                    <div class="sig-line"></div>
                    <div class="sig-label">Authorized Signature</div>
                    @if(!empty($branding->director_name))
                        <div class="sig-name">{{ $branding->director_name }}</div>
                    @endif
                    @if(!empty($branding->director_title))
                        <div class="sig-title">{{ $branding->director_title }}</div>
                    @endif
                </td>
                <td style="vertical-align: middle; padding-bottom: 2mm;">
                    <div class="badge-circle">
                        <div class="badge-text">CERTIFIED<br/>&#10003;</div>
                    </div>
                    <div class="cert-num">{{ 'CERT-' . str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</div>
                </td>
                <td>
                    <div style="height: 14mm;"></div>
                    <div class="sig-line"></div>
                    <div class="sig-label">Date Issued</div>
                    <div class="sig-name">{{ now()->format('d M Y') }}</div>
                </td>
            </tr>
        </table>

    </div>
</div>
</body>
</html>
