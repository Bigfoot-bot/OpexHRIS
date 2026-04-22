<?php

namespace App\Exports;

use App\Models\Tenant\Employee;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeesExport
{
    public function download()
    {
        $employees = Employee::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Employees');

        // Headers
        $headers = [
            'A' => 'Employee Number',
            'B' => 'First Name',
            'C' => 'Last Name',
            'D' => 'Email',
            'E' => 'Phone',
            'F' => 'Gender',
            'G' => 'Department',
            'H' => 'Job Title',
            'I' => 'Employment Type',
            'J' => 'Employment Status',
            'K' => 'Hire Date',
            'L' => 'Basic Salary',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        // Data
        $row = 2;
        foreach ($employees as $employee) {
            $sheet->setCellValue('A' . $row, $employee->employee_number);
            $sheet->setCellValue('B' . $row, $employee->first_name);
            $sheet->setCellValue('C' . $row, $employee->last_name);
            $sheet->setCellValue('D' . $row, $employee->email);
            $sheet->setCellValue('E' . $row, $employee->phone);
            $sheet->setCellValue('F' . $row, $employee->gender);
            $sheet->setCellValue('G' . $row, $employee->department);
            $sheet->setCellValue('H' . $row, $employee->job_title);
            $sheet->setCellValue('I' . $row, $employee->employment_type);
            $sheet->setCellValue('J' . $row, $employee->employment_status);
            $sheet->setCellValue('K' . $row, $employee->hire_date?->format('Y-m-d'));
            $sheet->setCellValue('L' . $row, $employee->basic_salary);
            $row++;
        }

        $filename = 'employees-' . now()->format('Y-m-d') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'export');

        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}