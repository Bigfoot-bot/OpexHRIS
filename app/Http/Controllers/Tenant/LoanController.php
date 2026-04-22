<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Loan;
use App\Models\Tenant\LoanRepayment;
use App\Models\Tenant\Employee;
use App\Models\Central\FacilityWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with('employee')->where('tenant_id', tenant('id'));
        if ($request->status) $query->where('status', $request->status);
        if ($request->employee_id) $query->where('employee_id', $request->employee_id);
        $loans     = $query->latest()->paginate(15);
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        $stats = [
            'pending'   => Loan::where('tenant_id', tenant('id'))->where('status', 'pending')->count(),
            'active'    => Loan::where('tenant_id', tenant('id'))->where('status', 'active')->count(),
            'total_disbursed' => Loan::where('tenant_id', tenant('id'))->whereIn('status', ['active', 'completed'])->sum('amount'),
            'total_balance'   => Loan::where('tenant_id', tenant('id'))->where('status', 'active')->sum('balance'),
        ];
        return view('tenant.loans.index', compact('loans', 'employees', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        return view('tenant.loans.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'      => ['required', 'exists:employees,id'],
            'type'             => ['required'],
            'amount'           => ['required', 'numeric', 'min:1'],
            'interest_rate'    => ['nullable', 'numeric', 'min:0'],
            'repayment_months' => ['required', 'integer', 'min:1'],
            'purpose'          => ['required', 'string'],
        ]);

        $amount       = $request->amount;
        $rate         = $request->interest_rate ?? 0;
        $months       = $request->repayment_months;
        $totalRepayable = $amount + ($amount * $rate / 100);
        $monthly      = round($totalRepayable / $months, 2);

        $count = Loan::where('tenant_id', tenant('id'))->count() + 1;
        $loanNumber = 'LN-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        Loan::create([
            'tenant_id'           => tenant('id'),
            'employee_id'         => $request->employee_id,
            'loan_number'         => $loanNumber,
            'type'                => $request->type,
            'amount'              => $amount,
            'interest_rate'       => $rate,
            'repayment_months'    => $months,
            'monthly_deduction'   => $monthly,
            'total_repayable'     => $totalRepayable,
            'balance'             => $totalRepayable,
            'purpose'             => $request->purpose,
            'status'              => 'pending',
        ]);

        // Notify admins
        $employee = \App\Models\Tenant\Employee::find($request->employee_id);
        $employeeName = $employee ? $employee->first_name . ' ' . $employee->last_name : 'Employee';
        \App\Services\NotificationService::loanRequested($employeeName, $loanNumber, $request->amount);

        // Email admins
        $adminUsers = \App\Models\Tenant\User::where('tenant_id', tenant('id'))->where('is_admin', 1)->get();
        foreach ($adminUsers as $admin) {
            \Illuminate\Support\Facades\Mail::send('emails.loan-requested', [
                'admin'    => $admin,
                'employee' => $employee,
                'loan'     => \App\Models\Tenant\Loan::where('loan_number', $loanNumber)->first(),
            ], function ($m) use ($admin) {
                $m->to($admin->email, $admin->name)->subject('New Loan Application - ' . tenant('name'));
            });
        }

        return redirect()->route('tenant.loans.index')->with('success', 'Loan application submitted successfully!');
    }

    public function show(Loan $loan)
    {
        if ($loan->tenant_id !== tenant('id')) abort(403);
        $loan->load(['employee', 'repayments']);
        return view('tenant.loans.show', compact('loan'));
    }

    public function approve(Loan $loan)
    {
        if ($loan->tenant_id !== tenant('id')) abort(403);
        $loan->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        $employee = $loan->employee;
        $employeeName = $employee->first_name . ' ' . $employee->last_name;

        // Send notification
        \App\Services\NotificationService::loanApproved($employeeName, $loan->loan_number, $loan->amount);

        // Send email
        if ($employee->email) {
            \Illuminate\Support\Facades\Mail::send('emails.loan-approved', [
                'employee' => $employee,
                'loan'     => $loan,
            ], function ($m) use ($employee) {
                $m->to($employee->email, $employee->first_name . ' ' . $employee->last_name)
                  ->subject('Loan Application Approved - ' . tenant('name'));
            });
        }

        return back()->with('success', 'Loan approved and employee notified!');
    }

    public function reject(Loan $loan)
    {
        if ($loan->tenant_id !== tenant('id')) abort(403);
        $loan->update(['status' => 'rejected']);
        $employee = $loan->employee;
        $employeeName = $employee->first_name . ' ' . $employee->last_name;

        // Send notification
        \App\Services\NotificationService::loanRejected($employeeName, $loan->loan_number);

        // Send email
        if ($employee->email) {
            \Illuminate\Support\Facades\Mail::send('emails.loan-rejected', [
                'employee' => $employee,
                'loan'     => $loan,
            ], function ($m) use ($employee) {
                $m->to($employee->email, $employee->first_name . ' ' . $employee->last_name)
                  ->subject('Loan Application Update - ' . tenant('name'));
            });
        }

        return back()->with('success', 'Loan rejected and employee notified!');
    }

    public function disburse(Loan $loan)
    {
        if ($loan->tenant_id !== tenant('id')) abort(403);
        if ($loan->status !== 'approved') return back()->with('error', 'Only approved loans can be disbursed.');

        // Check wallet balance
        $wallet = FacilityWallet::getOrCreate(tenant('id'));
        if (!$wallet->hasSufficientBalance($loan->amount)) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        // Deduct from wallet
        $wallet->debit($loan->amount, "Loan disbursement {$loan->loan_number}", 'loan', $loan->loan_number, auth()->user()->name);

        $disbursementDate   = now();
        $startRepaymentDate = now()->addMonth();

        $loan->update([
            'status'               => 'active',
            'disbursement_date'    => $disbursementDate,
            'start_repayment_date' => $startRepaymentDate,
        ]);

        // Generate repayment schedule
        for ($i = 1; $i <= $loan->repayment_months; $i++) {
            LoanRepayment::create([
                'tenant_id'   => tenant('id'),
                'loan_id'     => $loan->id,
                'employee_id' => $loan->employee_id,
                'amount'      => $loan->monthly_deduction,
                'due_date'    => $startRepaymentDate->copy()->addMonths($i - 1),
                'status'      => 'pending',
            ]);
        }

        $employee = $loan->employee;
        $employeeName = $employee->first_name . ' ' . $employee->last_name;
        $bankName = $employee->bank_name ?? 'your bank';
        $accountNumber = $employee->bank_account_number ?? 'N/A';

        // Send notification
        \App\Services\NotificationService::loanDisbursed($employeeName, $loan->loan_number, $loan->amount, $bankName, $accountNumber);

        // Send email
        if ($employee->email) {
            \Illuminate\Support\Facades\Mail::send('emails.loan-disbursed', [
                'employee'      => $employee,
                'loan'          => $loan,
                'bankName'      => $bankName,
                'accountNumber' => $accountNumber,
            ], function ($m) use ($employee) {
                $m->to($employee->email, $employee->first_name . ' ' . $employee->last_name)
                  ->subject('Loan Disbursed - ' . tenant('name'));
            });
        }

        return back()->with('success', 'Loan disbursed successfully! KES ' . number_format($loan->amount, 2) . ' deducted from wallet.');
    }

    public function recordPayment(Request $request, LoanRepayment $repayment)
    {
        $request->validate(['paid_date' => ['required', 'date']]);
        if ($repayment->tenant_id !== tenant('id')) abort(403);

        $repayment->update([
            'status'   => 'paid',
            'paid_date'=> $request->paid_date,
            'payment_method' => $request->payment_method ?? 'payroll_deduction',
        ]);

        // Update loan balance
        $loan = $repayment->loan;
        $newBalance = $loan->balance - $repayment->amount;
        $status = $newBalance <= 0 ? 'completed' : 'active';
        $loan->update(['balance' => max(0, $newBalance), 'status' => $status]);

        return back()->with('success', 'Payment recorded successfully!');
    }
}



