<?php

namespace App\Exports;

use App\Models\Tenant\PayrollRecord;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PayrollExport
{
    protected $periodId;

    public function __construct($periodId = null)
    {
        $this->periodId = $periodId;
    }

    public function download()
    {
        $query = PayrollRecord::with(['employee', 'payrollPeriod']);

        if ($this->periodId) {
            $query->where('payroll_period_id', $this->periodId);
        }

        $records = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Payroll');

        $headers = [
            'A' => 'Period',
            'B' => 'Employee Name',
            'C' => 'Employee Number',
            'D' => 'Department',
            'E' => 'Basic Salary',
            'F' => 'Gross Salary',
            'G' => 'PAYE',
            'H' => 'NHIF',
            'I' => 'NSSF',
            'J' => 'Housing Levy',
            'K' => 'Total Deductions',
            'L' => 'Net Salary',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A' . $row, $record->payrollPeriod->name);
            $sheet->setCellValue('B' . $row, $record->employee->full_name);
            $sheet->setCellValue('C' . $row, $record->employee->employee_number);
            $sheet->setCellValue('D' . $row, $record->employee->department);
            $sheet->setCellValue('E' . $row, $record->basic_salary);
            $sheet->setCellValue('F' . $row, $record->gross_salary);
            $sheet->setCellValue('G' . $row, $record->paye);
            $sheet->setCellValue('H' . $row, $record->nhif);
            $sheet->setCellValue('I' . $row, $record->nssf_employee);
            $sheet->setCellValue('J' . $row, $record->housing_levy);
            $sheet->setCellValue('K' . $row, $record->total_deductions);
            $sheet->setCellValue('L' . $row, $record->net_salary);
            $row++;
        }

        $filename = 'payroll-' . now()->format('Y-m-d') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'export');

        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}