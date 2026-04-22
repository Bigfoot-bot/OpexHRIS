<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
use App\Models\Tenant\LeaveRequest;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\PayrollPeriod;
use App\Models\Tenant\Attendance;
use App\Models\Tenant\TrainingProgram;
use Illuminate\Http\Request;

class ReportBuilderController extends Controller
{
    public function index()
    {
        $reportTypes = [
            'employees'  => 'Employee Report',
            'leave'      => 'Leave Report',
            'payroll'    => 'Payroll Report',
            'training'   => 'Training Report',
            'overtime'   => 'Overtime Report',
            'loans'      => 'Loans Report',
            'expenses'   => 'Expense Claims Report',
            'headcount'  => 'Headcount Report',
        ];
        return view('tenant.report-builder.index', compact('reportTypes'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => ['required', 'string'],
            'date_from'   => ['nullable', 'date'],
            'date_to'     => ['nullable', 'date'],
            'format'      => ['required', 'in:view,csv,excel'],
        ]);

        $type    = $request->report_type;
        $from    = $request->date_from;
        $to      = $request->date_to;
        $columns = $request->columns ?? [];
        $format  = $request->format;

        $data    = $this->getData($type, $from, $to, $request);
        $headers = $this->getHeaders($type, $columns);

        if ($format === 'csv') {
            return $this->exportCsv($data, $headers, $type);
        }

        return view('tenant.report-builder.result', compact('data', 'headers', 'type', 'request'));
    }

    private function getData(string $type, $from, $to, Request $request): array
    {
        $tenantId = tenant('id');

        switch ($type) {
            case 'employees':
                $query = Employee::where('tenant_id', $tenantId);
                if ($request->department) $query->where('department', $request->department);
                if ($request->employment_status) $query->where('employment_status', $request->employment_status);
                return $query->get()->map(fn($e) => [
                    'Employee No'    => $e->employee_number,
                    'Name'           => $e->first_name . ' ' . $e->last_name,
                    'Department'     => $e->department,
                    'Job Title'      => $e->job_title,
                    'Employment Type'=> $e->employment_type,
                    'Status'         => $e->employment_status,
                    'Date Joined'    => $e->date_of_joining,
                    'Basic Salary'   => number_format($e->basic_salary, 2),
                    'Email'          => $e->email,
                    'Phone'          => $e->phone,
                ])->toArray();

            case 'leave':
                $query = LeaveRequest::with('employee')->where('tenant_id', $tenantId);
                if ($from) $query->where('start_date', '>=', $from);
                if ($to) $query->where('end_date', '<=', $to);
                if ($request->status) $query->where('status', $request->status);
                return $query->get()->map(fn($l) => [
                    'Employee'    => $l->employee->first_name . ' ' . $l->employee->last_name,
                    'Leave Type'  => $l->leaveType->name ?? 'N/A',
                    'Start Date'  => $l->start_date,
                    'End Date'    => $l->end_date,
                    'Days'        => $l->days,
                    'Status'      => $l->status,
                    'Reason'      => $l->reason,
                ])->toArray();

            case 'payroll':
                $query = PayrollRecord::with(['employee', 'payrollPeriod'])->where('tenant_id', $tenantId);
                if ($from) $query->whereHas('payrollPeriod', fn($q) => $q->where('start_date', '>=', $from));
                if ($to) $query->whereHas('payrollPeriod', fn($q) => $q->where('end_date', '<=', $to));
                return $query->get()->map(fn($p) => [
                    'Period'       => $p->payrollPeriod->name ?? 'N/A',
                    'Employee'     => $p->employee->first_name . ' ' . $p->employee->last_name,
                    'Basic Salary' => number_format($p->basic_salary, 2),
                    'Gross Salary' => number_format($p->gross_salary, 2),
                    'PAYE'         => number_format($p->paye, 2),
                    'NHIF'         => number_format($p->nhif, 2),
                    'NSSF'         => number_format($p->nssf_employee, 2),
                    'Housing Levy' => number_format($p->housing_levy, 2),
                    'Net Salary'   => number_format($p->net_salary, 2),
                ])->toArray();

            case 'overtime':
                $query = \App\Models\Tenant\OvertimeRequest::with('employee')->where('tenant_id', $tenantId);
                if ($from) $query->where('date', '>=', $from);
                if ($to) $query->where('date', '<=', $to);
                if ($request->status) $query->where('status', $request->status);
                return $query->get()->map(fn($o) => [
                    'Employee'   => $o->employee->first_name . ' ' . $o->employee->last_name,
                    'Date'       => $o->date,
                    'Hours'      => $o->hours,
                    'Rate'       => $o->rate_multiplier . 'x',
                    'Amount'     => number_format($o->amount, 2),
                    'Status'     => $o->status,
                    'Reason'     => $o->reason,
                ])->toArray();

            case 'loans':
                $query = \App\Models\Tenant\Loan::with('employee')->where('tenant_id', $tenantId);
                if ($request->status) $query->where('status', $request->status);
                return $query->get()->map(fn($l) => [
                    'Loan No'    => $l->loan_number,
                    'Employee'   => $l->employee->first_name . ' ' . $l->employee->last_name,
                    'Type'       => str_replace('_', ' ', $l->type),
                    'Amount'     => number_format($l->amount, 2),
                    'Balance'    => number_format($l->balance, 2),
                    'Monthly'    => number_format($l->monthly_deduction, 2),
                    'Status'     => $l->status,
                ])->toArray();

            case 'expenses':
                $query = \App\Models\Tenant\ExpenseClaim::with('employee')->where('tenant_id', $tenantId);
                if ($from) $query->where('claim_date', '>=', $from);
                if ($to) $query->where('claim_date', '<=', $to);
                if ($request->status) $query->where('status', $request->status);
                return $query->get()->map(fn($e) => [
                    'Claim No'   => $e->claim_number,
                    'Employee'   => $e->employee->first_name . ' ' . $e->employee->last_name,
                    'Title'      => $e->title,
                    'Amount'     => number_format($e->total_amount, 2),
                    'Date'       => $e->claim_date,
                    'Status'     => $e->status,
                ])->toArray();

            case 'headcount':
                $byDept = Employee::where('tenant_id', $tenantId)
                    ->selectRaw('department, employment_status, COUNT(*) as count')
                    ->groupBy('department', 'employment_status')
                    ->get();
                return $byDept->map(fn($r) => [
                    'Department' => $r->department,
                    'Status'     => $r->employment_status,
                    'Count'      => $r->count,
                ])->toArray();

            default:
                return [];
        }
    }

    private function getHeaders(string $type, array $columns): array
    {
        $data = $this->getData($type, null, null, request());
        return !empty($data) ? array_keys($data[0]) : [];
    }

    private function exportCsv(array $data, array $headers, string $type): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = $type . '_report_' . date('Ymd') . '.csv';
        return response()->stream(function () use ($data, $headers) {
            $file = fopen('php://output', 'w');
            if (!empty($headers)) fputcsv($file, $headers);
            foreach ($data as $row) fputcsv($file, array_values($row));
            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
