<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #ffffff;
            color: #1a1a1a;
            width: 297mm;
            height: 210mm;
        }
        .page {
            width: 100%;
            height: 210mm;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 18mm 22mm;
        }
        /* Outer border */
        .border-outer {
            position: absolute;
            top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
            border: 3px solid #064e3b;
        }
        /* Inner border */
        .border-inner {
            position: absolute;
            top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
            border: 1px solid #064e3b;
        }
        /* Corner accents */
        .corner {
            position: absolute;
            width: 14mm;
            height: 14mm;
        }
        .corner-tl { top: 5mm;  left: 5mm;  border-top: 5px solid #064e3b; border-left: 5px solid #064e3b; }
        .corner-tr { top: 5mm;  right: 5mm; border-top: 5px solid #064e3b; border-right: 5px solid #064e3b; }
        .corner-bl { bottom: 5mm; left: 5mm;  border-bottom: 5px solid #064e3b; border-left: 5px solid #064e3b; }
        .corner-br { bottom: 5mm; right: 5mm; border-bottom: 5px solid #064e3b; border-right: 5px solid #064e3b; }

        .content {
            position: relative;
            z-index: 10;
            text-align: center;
            width: 100%;
        }
        .org-name {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 3px;
            color: #064e3b;
            text-transform: uppercase;
            margin-bottom: 4mm;
        }
        .divider {
            width: 60mm;
            height: 1.5px;
            background: #064e3b;
            margin: 0 auto 6mm;
        }
        .cert-title {
            font-size: 28pt;
            font-weight: bold;
            color: #064e3b;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }
        .cert-subtitle {
            font-size: 9pt;
            color: #6b7280;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 8mm;
        }
        .presented-to {
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 3mm;
            letter-spacing: 1px;
        }
        .employee-name {
            font-size: 26pt;
            font-weight: bold;
            color: #1f2937;
            font-style: italic;
            margin-bottom: 6mm;
            border-bottom: 1.5px solid #d1d5db;
            display: inline-block;
            padding-bottom: 2mm;
            min-width: 120mm;
        }
        .completed-text {
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 3mm;
        }
        .training-title {
            font-size: 14pt;
            font-weight: bold;
            color: #064e3b;
            margin-bottom: 6mm;
        }
        .meta-row {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 8mm;
        }
        .meta-row span {
            margin: 0 4mm;
        }
        .meta-label {
            font-weight: bold;
            color: #374151;
        }
        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 6mm;
            padding: 0 10mm;
        }
        .sig-block {
            text-align: center;
            width: 55mm;
        }
        .sig-line {
            border-top: 1px solid #374151;
            margin-bottom: 2mm;
        }
        .sig-label {
            font-size: 7pt;
            color: #6b7280;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .cert-badge {
            width: 24mm;
            height: 24mm;
            border-radius: 50%;
            border: 3px solid #064e3b;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .cert-badge-inner {
            font-size: 7pt;
            color: #064e3b;
            font-weight: bold;
            letter-spacing: 1px;
            text-align: center;
            line-height: 1.4;
        }
        .cert-number {
            font-size: 7pt;
            color: #9ca3af;
            margin-top: 2mm;
        }
    </style>
</head>
<body>
<div class="page">
    <!-- Decorative borders -->
    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>

    <div class="content">

        <div class="org-name">{{ strtoupper($org) }}</div>
        <div class="divider"></div>

        <div class="cert-title">Certificate</div>
        <div class="cert-subtitle">of Completion</div>

        <div class="presented-to">This is to certify that</div>

        <div class="employee-name">{{ $employee->full_name }}</div>

        <div class="completed-text">has successfully completed the training program</div>

        <div class="training-title">{{ $training->title }}</div>

        <div class="meta-row">
            <span><span class="meta-label">Category:</span> {{ ucwords(str_replace('_', ' ', $training->category)) }}</span>
            @if($training->provider)
            <span><span class="meta-label">Provider:</span> {{ $training->provider }}</span>
            @endif
            <span><span class="meta-label">Date:</span>
                {{ $enrollment->completion_date
                    ? $enrollment->completion_date->format('F d, Y')
                    : $training->end_date->format('F d, Y') }}
            </span>
            @if($enrollment->cpd_points_earned)
            <span><span class="meta-label">CPD Points:</span> {{ $enrollment->cpd_points_earned }}</span>
            @endif
        </div>

        <div class="footer-row">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Authorized Signature</div>
            </div>

            <div class="cert-badge">
                <div class="cert-badge-inner">CERTIFIED<br>&#10003;</div>
            </div>

            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Date Issued</div>
                <div class="cert-number">{{ now()->format('d M Y') }}</div>
            </div>
        </div>

    </div>
</div>
</body>
</html>
