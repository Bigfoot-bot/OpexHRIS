<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PayrollPeriod;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatutoryReturnsController extends Controller
{
    public function index()
    {
        $years   = range(date('Y') - 2, date('Y'));
        $periods = PayrollPeriod::where('tenant_id', tenant('id'))->where('status', 'approved')->latest()->get();
        return view('tenant.statutory.index', compact('years', 'periods'));
    }

    // P9 - Annual Tax Deduction Card per Employee
    public function p9(Request $request)
    {
        $request->validate(['year' => ['required', 'integer']]);
        $year = $request->year;

        $records = PayrollRecord::where('tenant_id', tenant('id'))
                    ->whereHas('payrollPeriod', fn($q) => $q->where('year', $year))
                    ->with(['employee', 'payrollPeriod'])
                    ->get()
                    ->groupBy('employee_id');

        $filename = "P9_Form_{$year}_" . date('Ymd') . ".csv";
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($records, $year) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['P9 Annual Tax Deduction Card - ' . $year]);
            fputcsv($file, []);
            fputcsv($file, [
                'Employee No', 'Employee Name', 'KRA PIN', 'NHIF No', 'NSSF No',
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
                'Total Gross', 'Total PAYE', 'Total NHIF', 'Total NSSF', 'Total Housing Levy', 'Total Net'
            ]);

            foreach ($records as $employeeId => $empRecords) {
                $emp = $empRecords->first()->employee;
                $monthly = array_fill(0, 12, 0);
                foreach ($empRecords as $record) {
                    $month = $record->payrollPeriod->month - 1;
                    $monthly[$month] = $record->gross_salary;
                }
                fputcsv($file, array_merge([
                    $emp->employee_number,
                    $emp->first_name . ' ' . $emp->last_name,
                    $emp->kra_pin ?? '',
                    $emp->nhif_number ?? '',
                    $emp->nssf_number ?? '',
                ], $monthly, [
                    $empRecords->sum('gross_salary'),
                    $empRecords->sum('paye'),
                    $empRecords->sum('nhif'),
                    $empRecords->sum('nssf_employee'),
                    $empRecords->sum('housing_levy'),
                    $empRecords->sum('net_salary'),
                ]));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // P10 - Monthly PAYE Return
    public function p10(Request $request)
    {
        $request->validate(['period_id' => ['required', 'exists:payroll_periods,id']]);
        $period  = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::where('payroll_period_id', $period->id)
                    ->where('tenant_id', tenant('id'))
                    ->with('employee')->get();

        $filename = "P10_Return_{$period->name}_" . date('Ymd') . ".csv";
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($records, $period) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['P10 Monthly PAYE Return - ' . $period->name]);
            fputcsv($file, []);
            fputcsv($file, ['Employee No', 'Employee Name', 'KRA PIN', 'Gross Pay', 'Taxable Pay', 'PAYE Tax', 'Personal Relief', 'Net PAYE']);
            foreach ($records as $record) {
                $emp = $record->employee;
                $personalRelief = 2400; // Monthly personal relief
                fputcsv($file, [
                    $emp->employee_number,
                    $emp->first_name . ' ' . $emp->last_name,
                    $emp->kra_pin ?? '',
                    number_format($record->gross_salary, 2, '.', ''),
                    number_format($record->gross_salary, 2, '.', ''),
                    number_format($record->paye, 2, '.', ''),
                    number_format($personalRelief, 2, '.', ''),
                    number_format(max(0, $record->paye - $personalRelief), 2, '.', ''),
                ]);
            }
            fputcsv($file, []);
            fputcsv($file, ['', '', 'TOTALS',
                number_format($records->sum('gross_salary'), 2, '.', ''),
                number_format($records->sum('gross_salary'), 2, '.', ''),
                number_format($records->sum('paye'), 2, '.', ''),
                '',
                '',
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // NHIF Monthly Return
    public function nhif(Request $request)
    {
        $request->validate(['period_id' => ['required', 'exists:payroll_periods,id']]);
        $period  = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::where('payroll_period_id', $period->id)
                    ->where('tenant_id', tenant('id'))
                    ->with('employee')->get();

        $filename = "NHIF_Return_{$period->name}_" . date('Ymd') . ".csv";
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($records, $period) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['NHIF Monthly Return - ' . $period->name]);
            fputcsv($file, []);
            fputcsv($file, ['Employee No', 'Employee Name', 'NHIF Number', 'ID Number', 'Gross Salary', 'NHIF Contribution']);
            foreach ($records as $record) {
                $emp = $record->employee;
                fputcsv($file, [
                    $emp->employee_number,
                    $emp->first_name . ' ' . $emp->last_name,
                    $emp->nhif_number ?? '',
                    $emp->national_id ?? '',
                    number_format($record->gross_salary, 2, '.', ''),
                    number_format($record->nhif, 2, '.', ''),
                ]);
            }
            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTAL', number_format($records->sum('gross_salary'), 2, '.', ''), number_format($records->sum('nhif'), 2, '.', '')]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // NSSF Monthly Return
    public function nssf(Request $request)
    {
        $request->validate(['period_id' => ['required', 'exists:payroll_periods,id']]);
        $period  = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::where('payroll_period_id', $period->id)
                    ->where('tenant_id', tenant('id'))
                    ->with('employee')->get();

        $filename = "NSSF_Return_{$period->name}_" . date('Ymd') . ".csv";
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($records, $period) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['NSSF Monthly Return - ' . $period->name]);
            fputcsv($file, []);
            fputcsv($file, ['Employee No', 'Employee Name', 'NSSF Number', 'ID Number', 'Gross Salary', 'Employee NSSF', 'Employer NSSF', 'Total NSSF']);
            foreach ($records as $record) {
                $emp = $record->employee;
                fputcsv($file, [
                    $emp->employee_number,
                    $emp->first_name . ' ' . $emp->last_name,
                    $emp->nssf_number ?? '',
                    $emp->national_id ?? '',
                    number_format($record->gross_salary, 2, '.', ''),
                    number_format($record->nssf_employee, 2, '.', ''),
                    number_format($record->nssf_employer, 2, '.', ''),
                    number_format($record->nssf_employee + $record->nssf_employer, 2, '.', ''),
                ]);
            }
            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'TOTALS',
                number_format($records->sum('gross_salary'), 2, '.', ''),
                number_format($records->sum('nssf_employee'), 2, '.', ''),
                number_format($records->sum('nssf_employer'), 2, '.', ''),
                number_format($records->sum('nssf_employee') + $records->sum('nssf_employer'), 2, '.', ''),
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Housing Levy Return
    public function housingLevy(Request $request)
    {
        $request->validate(['period_id' => ['required', 'exists:payroll_periods,id']]);
        $period  = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::where('payroll_period_id', $period->id)
                    ->where('tenant_id', tenant('id'))
                    ->with('employee')->get();

        $filename = "HousingLevy_Return_{$period->name}_" . date('Ymd') . ".csv";
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($records, $period) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Affordable Housing Levy Return - ' . $period->name]);
            fputcsv($file, []);
            fputcsv($file, ['Employee No', 'Employee Name', 'KRA PIN', 'Gross Salary', 'Employee Levy (1.5%)', 'Employer Levy (1.5%)', 'Total Levy']);
            foreach ($records as $record) {
                $emp = $record->employee;
                $employerLevy = $record->gross_salary * 0.015;
                fputcsv($file, [
                    $emp->employee_number,
                    $emp->first_name . ' ' . $emp->last_name,
                    $emp->kra_pin ?? '',
                    number_format($record->gross_salary, 2, '.', ''),
                    number_format($record->housing_levy, 2, '.', ''),
                    number_format($employerLevy, 2, '.', ''),
                    number_format($record->housing_levy + $employerLevy, 2, '.', ''),
                ]);
            }
            fputcsv($file, []);
            fputcsv($file, ['', '', 'TOTALS',
                number_format($records->sum('gross_salary'), 2, '.', ''),
                number_format($records->sum('housing_levy'), 2, '.', ''),
                number_format($records->sum('gross_salary') * 0.015, 2, '.', ''),
                number_format($records->sum('housing_levy') + ($records->sum('gross_salary') * 0.015), 2, '.', ''),
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}


