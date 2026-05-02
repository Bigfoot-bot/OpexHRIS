<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tenant\Employee;
use App\Models\Tenant\PayrollPeriod;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\User;
use App\Services\PayrollService;
use App\Services\NotificationService;
use App\Mail\PayslipPublished;
use App\Models\Central\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected PayrollService $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index(Request $request)
    {
        $branches = \App\Models\Branch::where('tenant_id', tenant('id'))->get();
        $query = PayrollPeriod::query();
        if ($request->branch_id) {
            $employeeIds = Employee::where('branch_id', $request->branch_id)->pluck('id');
            $query->whereHas('records', fn($q) => $q->whereIn('employee_id', $employeeIds));
        }
        $periods = $query->latest()->paginate(12);
        return view('tenant.payroll.index', compact('periods', 'branches'));
    }

    public function create()
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->format('F');
        }
        $years = range(date('Y') - 1, date('Y') + 1);
        return view('tenant.payroll.create', compact('months', 'years'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month'        => ['required', 'integer', 'between:1,12'],
            'year'         => ['required', 'integer'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $month = $validated['month'];
        $year  = $validated['year'];

        $exists = PayrollPeriod::where('tenant_id', tenant('id'))
                               ->where('month', $month)
                               ->where('year', $year)
                               ->first();

        if ($exists) {
            return redirect()->route('tenant.payroll.show', $exists)
                             ->with('error', "Payroll for " . Carbon::create($year, $month, 1)->format('F Y') . " has already been generated. You can view or edit it below.");
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $month, 1)->endOfMonth();

        $period = PayrollPeriod::create([
            'tenant_id'    => tenant('id'),
            'name'         => Carbon::create($year, $month, 1)->format('F Y'),
            'month'        => $month,
            'year'         => $year,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'payment_date' => $validated['payment_date'] ?? null,
            'status'       => 'draft',
        ]);

        $employees = Employee::where('employment_status', 'active')->get();

        foreach ($employees as $employee) {
            $calculated = $this->payrollService->calculate(
                basicSalary: (float) $employee->basic_salary,
            );

            PayrollRecord::create(array_merge($calculated, [
                'tenant_id'         => tenant('id'),
                'payroll_period_id' => $period->id,
                'employee_id'       => $employee->id,
            ]));
        }

        NotificationService::payrollGenerated($period->name, $employees->count());
        AuditLog::log('created', 'Payroll', "Generated payroll for {$period->name} - {$employees->count()} employees");

        return redirect()->route('tenant.payroll.show', $period)
                         ->with('success', "Payroll for {$period->name} created with {$employees->count()} employee(s).");
    }

    public function show(PayrollPeriod $payroll)
    {
        $payroll->load('records.employee');
        return view('tenant.payroll.show', compact('payroll'));
    }

    public function approve(Request $request, PayrollPeriod $payroll)
    {
        $paymentMode = $request->input('payment_mode', 'manual');
        $payroll->load('records');
        $totalNetPay = $payroll->records->sum('net_salary');

        // Check wallet balance if wallet mode
        if ($paymentMode === 'wallet') {
            $wallet = \App\Models\Central\FacilityWallet::getOrCreate(tenant('id'));
            if (!$wallet->hasSufficientBalance($totalNetPay)) {
                return back()->with('error', 'Insufficient wallet balance. Please top up your wallet first.');
            }
            // Debit wallet
            $wallet->debit(
                $totalNetPay,
                'Payroll payment - ' . $payroll->name,
                'payroll',
                'PAY-' . $payroll->id,
                auth()->user()->name
            );
        }

        $payroll->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'payment_mode' => $paymentMode,
        ]);

        NotificationService::payrollApproved($payroll->name);
        AuditLog::log('approved', 'Payroll', "Approved payroll for {$payroll->name}");

        // Email each employee their payslip with PDF attachment
        try {
            $payroll->load('records.employee');
            $tenant   = Tenant::find(tenant('id'));
            $logoPath = ($tenant->logo && file_exists(public_path('logos/' . $tenant->logo)))
                        ? public_path('logos/' . $tenant->logo)
                        : null;

            foreach ($payroll->records as $record) {
                $employee = $record->employee;
                $user     = User::where('tenant_id', tenant('id'))
                                ->where('employee_id', $employee->id)
                                ->first();
                if (!$user) continue;

                $pdf = Pdf::loadView('tenant.payroll.payslip', compact('payroll', 'record', 'tenant', 'logoPath'));
                $pdf->setPaper('a4', 'portrait');
                $pdfContent  = $pdf->output();
                $pdfFilename = 'Payslip-' . $employee->employee_number . '-' . $payroll->name . '.pdf';

                $link = 'https://' . request()->getHost() . '/my/payslips';
                Mail::to($user->email)->send(new PayslipPublished(
                    $user,
                    $payroll->name,
                    $record->net_salary,
                    $link,
                    $pdfContent,
                    $pdfFilename
                ));
            }
        } catch (\Exception $e) {
            // Silently fail — payroll is already approved
        }

        return back()->with('success', 'Payroll approved and employees notified!');
    }

    public function updateRecord(Request $request, PayrollRecord $record)
    {
        $validated = $request->validate([
            'house_allowance'     => ['nullable', 'numeric'],
            'transport_allowance' => ['nullable', 'numeric'],
            'medical_allowance'   => ['nullable', 'numeric'],
            'other_allowances'    => ['nullable', 'numeric'],
            'overtime_pay'        => ['nullable', 'numeric'],
            'loan_deduction'      => ['nullable', 'numeric'],
            'other_deductions'    => ['nullable', 'numeric'],
        ]);

        $calculated = $this->payrollService->calculate(
            basicSalary:         (float) $record->basic_salary,
            houseAllowance:      (float) ($validated['house_allowance'] ?? 0),
            transportAllowance:  (float) ($validated['transport_allowance'] ?? 0),
            medicalAllowance:    (float) ($validated['medical_allowance'] ?? 0),
            otherAllowances:     (float) ($validated['other_allowances'] ?? 0),
            overtimePay:         (float) ($validated['overtime_pay'] ?? 0),
            loanDeduction:       (float) ($validated['loan_deduction'] ?? 0),
            otherDeductions:     (float) ($validated['other_deductions'] ?? 0),
        );

        $record->update($calculated);
        AuditLog::log('updated', 'Payroll', "Updated payroll record for {$record->employee->full_name}");

        return back()->with('success', 'Payroll record updated successfully!');
    }
}





