<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PayrollPeriod;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BankFileController extends Controller
{
    public function index()
    {
        $periods = PayrollPeriod::where('tenant_id', tenant('id'))
                                ->where('status', 'approved')
                                ->latest()->get();
        return view('tenant.bank-files.index', compact('periods'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'period_id' => ['required', 'exists:payroll_periods,id'],
            'format'    => ['required', 'in:kcb,equity,cooperative,standard,generic'],
        ]);

        $period  = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::where('payroll_period_id', $period->id)
                                ->where('tenant_id', tenant('id'))
                                ->with('employee')
                                ->get();

        $format   = $request->format;
        $filename = "bank_file_{$period->name}_{$format}_" . date('Ymd') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($records, $format, $period) {
            $file = fopen('php://output', 'w');

            // Headers based on bank format
            if ($format === 'kcb') {
                fputcsv($file, ['Account Number', 'Account Name', 'Amount', 'Narration', 'Currency']);
                foreach ($records as $record) {
                    $emp = $record->employee;
                    fputcsv($file, [
                        $emp->bank_account_number ?? '',
                        $emp->first_name . ' ' . $emp->last_name,
                        number_format($record->net_salary, 2, '.', ''),
                        'Salary ' . $period->name,
                        'KES',
                    ]);
                }
            } elseif ($format === 'equity') {
                fputcsv($file, ['BeneficiaryName', 'BeneficiaryAccount', 'BeneficiaryBank', 'BeneficiaryBranch', 'Amount', 'PaymentDetails']);
                foreach ($records as $record) {
                    $emp = $record->employee;
                    fputcsv($file, [
                        $emp->first_name . ' ' . $emp->last_name,
                        $emp->bank_account_number ?? '',
                        $emp->bank_name ?? '',
                        $emp->bank_branch ?? '',
                        number_format($record->net_salary, 2, '.', ''),
                        'Salary Payment ' . $period->name,
                    ]);
                }
            } elseif ($format === 'cooperative') {
                fputcsv($file, ['Employee Name', 'Account No', 'Bank Code', 'Branch Code', 'Net Pay', 'Reference']);
                foreach ($records as $record) {
                    $emp = $record->employee;
                    fputcsv($file, [
                        $emp->first_name . ' ' . $emp->last_name,
                        $emp->bank_account_number ?? '',
                        $emp->bank_code ?? '',
                        $emp->bank_branch ?? '',
                        number_format($record->net_salary, 2, '.', ''),
                        'SAL/' . $period->name . '/' . $emp->employee_number,
                    ]);
                }
            } else {
                // Generic format
                fputcsv($file, ['Employee No', 'Employee Name', 'Bank Name', 'Account Number', 'Branch', 'Net Salary', 'Period']);
                foreach ($records as $record) {
                    $emp = $record->employee;
                    fputcsv($file, [
                        $emp->employee_number,
                        $emp->first_name . ' ' . $emp->last_name,
                        $emp->bank_name ?? '',
                        $emp->bank_account_number ?? '',
                        $emp->bank_branch ?? '',
                        number_format($record->net_salary, 2, '.', ''),
                        $period->name,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function generateExpense(Request $request)
    {
        $request->validate(['month' => ['required']]);

        $claims = \App\Models\Tenant\ExpenseClaim::where('tenant_id', tenant('id'))
                    ->where('status', 'approved')
                    ->whereMonth('claim_date', Carbon::parse($request->month)->month)
                    ->whereYear('claim_date', Carbon::parse($request->month)->year)
                    ->with('employee')
                    ->get();

        $filename = "expense_payments_" . $request->month . "_" . date('Ymd') . ".csv";
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($claims) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employee No', 'Employee Name', 'Claim No', 'Bank Name', 'Account Number', 'Amount', 'Description']);
            foreach ($claims as $claim) {
                $emp = $claim->employee;
                fputcsv($file, [
                    $emp->employee_number ?? '',
                    $emp->first_name . ' ' . $emp->last_name,
                    $claim->claim_number,
                    $emp->bank_name ?? '',
                    $emp->bank_account_number ?? '',
                    number_format($claim->total_amount, 2, '.', ''),
                    $claim->title,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
