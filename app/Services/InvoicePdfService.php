<?php

namespace App\Services;

use App\Models\SubscriptionInvoice;
use App\Models\BrandingSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InvoicePdfService
{
    public static function generate(SubscriptionInvoice $invoice, string $tenantId): string
    {
        $tenant   = DB::table('tenants')->where('id', $tenantId)->first();
        $branding = BrandingSetting::getSettings();
        $settings = \App\Models\Central\DarajaSetting::getSettings();
        $logoPath = $branding->logo ? public_path('branding/' . $branding->logo) : null;

        $data = [
            'invoice'      => $invoice,
            'isPaid'       => $invoice->status === 'paid',
            'planName'     => $invoice->plan->name ?? 'N/A',
            'tenantName'   => $tenant->name ?? 'N/A',
            'tenantEmail'  => $tenant->email ?? '',
            'tenantSlug'   => $tenant->slug ?? $tenantId,
            'brandingName' => $branding->platform_name ?? 'OpEx HRIS',
            'brandingLogo' => $logoPath,
            'paybill'      => $settings->paybill_number ?? 'N/A',
        ];

        $pdf      = Pdf::loadView('pdf.invoice', $data);
        $filename = ($invoice->status === 'paid' ? 'receipt_' : 'invoice_') . $invoice->invoice_number . '.pdf';
        $path     = storage_path('app/invoices/' . $filename);

        if (!file_exists(storage_path('app/invoices'))) {
            mkdir(storage_path('app/invoices'), 0755, true);
        }

        $pdf->save($path);
        return $path;
    }
}
