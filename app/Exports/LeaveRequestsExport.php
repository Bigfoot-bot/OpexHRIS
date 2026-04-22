<?php

namespace App\Exports;

use App\Models\Tenant\LeaveRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LeaveRequestsExport
{
    public function download()
    {
        $requests = LeaveRequest::with(['employee', 'leaveType'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leave Requests');

        $headers = [
            'A' => 'Employee Name',
            'B' => 'Employee Number',
            'C' => 'Leave Type',
            'D' => 'Start Date',
            'E' => 'End Date',
            'F' => 'Days Requested',
            'G' => 'Status',
            'H' => 'Reason',
            'I' => 'Applied On',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $row = 2;
        foreach ($requests as $leave) {
            $sheet->setCellValue('A' . $row, $leave->employee->full_name);
            $sheet->setCellValue('B' . $row, $leave->employee->employee_number);
            $sheet->setCellValue('C' . $row, $leave->leaveType->name);
            $sheet->setCellValue('D' . $row, $leave->start_date->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $leave->end_date->format('Y-m-d'));
            $sheet->setCellValue('F' . $row, $leave->days_requested);
            $sheet->setCellValue('G' . $row, $leave->status);
            $sheet->setCellValue('H' . $row, $leave->reason);
            $sheet->setCellValue('I' . $row, $leave->created_at->format('Y-m-d'));
            $row++;
        }

        $filename = 'leave-requests-' . now()->format('Y-m-d') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'export');

        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}