<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\Tenant\PayrollPeriod;
use App\Models\Tenant\PayrollRecord;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipController extends Controller
{
    public function download(PayrollPeriod $payroll, PayrollRecord $record)
    {
        $record->load('employee');
        $payroll->load('records');

        $tenant     = Tenant::find(tenant('id'));
        $logoPath   = null;
        if ($tenant->logo && file_exists(public_path('logos/' . $tenant->logo))) {
            $logoPath = public_path('logos/' . $tenant->logo);
        }

        $pdf = Pdf::loadView('tenant.payroll.payslip', compact('payroll', 'record', 'tenant', 'logoPath'));
        $pdf->setPaper('a4', 'portrait');
        $filename = 'payslip-' . $record->employee->employee_number . '-' . $payroll->name . '.pdf';
        return $pdf->download($filename);
    }

    public function downloadAll(PayrollPeriod $payroll)
    {
        $payroll->load('records.employee');

        $tenant     = Tenant::find(tenant('id'));
        $logoPath   = null;
        if ($tenant->logo && file_exists(public_path('logos/' . $tenant->logo))) {
            $logoPath = public_path('logos/' . $tenant->logo);
        }

        $pdf = Pdf::loadView('tenant.payroll.payslip-all', compact('payroll', 'tenant', 'logoPath'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('payroll-' . $payroll->name . '.pdf');
    }
}
