<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 30px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #064e3b; padding-bottom: 20px; }
        .company h1 { font-size: 20px; color: #064e3b; font-weight: bold; margin-top: 5px; }
        .company p { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .logo { height: 50px; width: 50px; object-fit: contain; margin-bottom: 5px; }
        .doc-type { text-align: right; }
        .doc-type h2 { font-size: 24px; font-weight: bold; color: {{ $isPaid ? '#065f46' : '#d97706' }}; }
        .doc-type p { font-size: 11px; color: #6b7280; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .info-box h3 { font-size: 10px; text-transform: uppercase; color: #6b7280; margin-bottom: 8px; }
        .info-box p { font-size: 12px; color: #333; margin-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; text-align: left; padding: 8px 12px; font-size: 11px; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
        td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
        .totals { width: 40%; margin-left: auto; }
        .totals td { padding: 5px 12px; }
        .total-row td { font-weight: bold; font-size: 14px; color: #064e3b; border-top: 2px solid #e5e7eb; }
        .payment-box { background: #f0fdf4; border: 1px solid #d1fae5; border-radius: 6px; padding: 15px; margin-top: 20px; }
        .payment-box h3 { font-size: 11px; text-transform: uppercase; color: #065f46; margin-bottom: 8px; }
        .payment-grid { display: flex; gap: 30px; }
        .payment-col p { font-size: 11px; color: #374151; margin-bottom: 3px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 15px; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; background: {{ $isPaid ? '#dcfce7' : '#fef3c7' }}; color: {{ $isPaid ? '#065f46' : '#92400e' }}; }
    </style>
</head>
<body>

    <div class="header">
        <div class="company">
            @if($brandingLogo)
                <img src="{{ $brandingLogo }}" class="logo" alt="Logo"/>
            @endif
            <h1>{{ $brandingName }}</h1>
            <p>Healthcare HR Management Platform</p>
        </div>
        <div class="doc-type">
            <h2>{{ $isPaid ? 'RECEIPT' : 'INVOICE' }}</h2>
            <p>{{ $isPaid ? $invoice->receipt_number : $invoice->invoice_number }}</p>
            <p>Date: {{ $invoice->created_at->format('M d, Y') }}</p>
            <br/>
            <span class="status-badge">{{ strtoupper($invoice->status) }}</span>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Billed To</h3>
            <p><strong>{{ $tenantName }}</strong></p>
            <p>{{ $tenantEmail }}</p>
        </div>
        <div class="info-box" style="text-align:right;">
            <h3>{{ $isPaid ? 'Payment Date' : 'Due Date' }}</h3>
            <p><strong>{{ $isPaid ? $invoice->paid_at->format('M d, Y') : $invoice->due_date->format('M d, Y') }}</strong></p>
            @if($isPaid)
            <p style="margin-top:5px;">Receipt No: <strong>{{ $invoice->receipt_number }}</strong></p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Billing Cycle</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $planName }} Plan Subscription</td>
                <td style="text-transform:capitalize;">{{ $invoice->cycle }}</td>
                <td style="text-align:right;">KES {{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td style="text-align:right;">KES {{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->discount_amount > 0)
        <tr>
            <td style="color:#065f46;">Discount</td>
            <td style="text-align:right; color:#065f46;">- KES {{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td>VAT ({{ $invoice->vat_percentage }}%)</td>
            <td style="text-align:right;">KES {{ number_format($invoice->vat_amount, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Total</td>
            <td style="text-align:right;">KES {{ number_format($invoice->total, 2) }}</td>
        </tr>
    </table>

    @if(!$isPaid)
    <div class="payment-box">
        <h3>Payment Instructions</h3>
        <div class="payment-grid">
            <div class="payment-col">
                <p><strong>M-Pesa Paybill:</strong></p>
                <p>Paybill: {{ $paybill }}</p>
                <p>Account: {{ $tenantSlug }}</p>
                <p>Amount: KES {{ number_format($invoice->total, 2) }}</p>
            </div>
            <div class="payment-col">
                <p><strong>Bank Transfer:</strong></p>
                <p>Bank: KCB Bank Kenya</p>
                <p>Account: OpEx Healthcare Consultancy Ltd</p>
                <p>Acc No: 1234567890</p>
                <p>Ref: {{ $tenantSlug }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for using {{ $brandingName }} &middot; Generated on {{ now()->format('M d, Y H:i') }}</p>
    </div>

</body>
</html>


