<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\ExpenseClaim;
use App\Models\Tenant\ExpenseItem;
use App\Models\Tenant\Employee;
use App\Models\Central\FacilityWallet;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpenseClaim::with('employee')->where('tenant_id', tenant('id'));
        if ($request->status) $query->where('status', $request->status);
        if ($request->employee_id) $query->where('employee_id', $request->employee_id);
        $claims    = $query->latest()->paginate(15);
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        $stats = [
            'pending'  => ExpenseClaim::where('tenant_id', tenant('id'))->whereIn('status', ['submitted'])->count(),
            'approved' => ExpenseClaim::where('tenant_id', tenant('id'))->where('status', 'approved')->count(),
            'paid'     => ExpenseClaim::where('tenant_id', tenant('id'))->where('status', 'paid')->count(),
            'total'    => ExpenseClaim::where('tenant_id', tenant('id'))->where('status', 'paid')->sum('total_amount'),
        ];
        return view('tenant.expenses.index', compact('claims', 'employees', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        return view('tenant.expenses.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'title'       => ['required', 'string'],
            'claim_date'  => ['required', 'date'],
            'items'       => ['required', 'array', 'min:1'],
        ]);

        $count = ExpenseClaim::where('tenant_id', tenant('id'))->count() + 1;
        $claimNumber = 'EXP-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $claim = ExpenseClaim::create([
            'tenant_id'    => tenant('id'),
            'employee_id'  => $request->employee_id,
            'claim_number' => $claimNumber,
            'title'        => $request->title,
            'claim_date'   => $request->claim_date,
            'description'  => $request->description,
            'status'       => 'draft',
            'total_amount' => 0,
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            if (empty($item['description']) || empty($item['amount'])) continue;
            $receiptPath = null;
            $receiptName = null;
            if (isset($item['receipt']) && $item['receipt']->isValid()) {
                $file = $item['receipt'];
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('receipts/' . tenant('id')), $filename);
                $receiptPath = 'receipts/' . tenant('id') . '/' . $filename;
                $receiptName = $file->getClientOriginalName();
            }
            ExpenseItem::create([
                'expense_claim_id' => $claim->id,
                'category'         => $item['category'] ?? 'other',
                'description'      => $item['description'],
                'date'             => $item['date'] ?? $request->claim_date,
                'amount'           => $item['amount'],
                'receipt_path'     => $receiptPath,
                'receipt_name'     => $receiptName,
            ]);
            $total += $item['amount'];
        }

        $claim->update(['total_amount' => $total]);
        return redirect()->route('tenant.expenses.index')->with('success', 'Expense claim created successfully!');
    }

    public function show(ExpenseClaim $expense)
    {
        if ($expense->tenant_id !== tenant('id')) abort(403);
        $expense->load(['employee', 'items']);
        return view('tenant.expenses.show', compact('expense'));
    }

    public function submit(ExpenseClaim $expense)
    {
        if ($expense->tenant_id !== tenant('id')) abort(403);
        $expense->update(['status' => 'submitted']);

        $employee = $expense->employee;
        $employeeName = $employee ? $employee->first_name . ' ' . $employee->last_name : 'Employee';

        // Notify admins
        \App\Services\NotificationService::expenseSubmitted($employeeName, $expense->claim_number, $expense->total_amount);

        // Email admins
        $adminUsers = \App\Models\Tenant\User::where('tenant_id', tenant('id'))->where('is_admin', 1)->get();
        foreach ($adminUsers as $admin) {
            \Illuminate\Support\Facades\Mail::send('emails.expense-submitted', [
                'admin'   => $admin,
                'expense' => $expense,
                'employee'=> $employee,
            ], function ($m) use ($admin) {
                $m->to($admin->email, $admin->name)->subject('New Expense Claim - ' . tenant('name'));
            });
        }

        return back()->with('success', 'Expense claim submitted for approval!');
    }

    public function approve(ExpenseClaim $expense)
    {
        if ($expense->tenant_id !== tenant('id')) abort(403);
        $expense->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $employee = $expense->employee;
        $employeeName = $employee ? $employee->first_name . ' ' . $employee->last_name : 'Employee';

        // Notify employee
        \App\Services\NotificationService::expenseApproved($employeeName, $expense->claim_number, $expense->total_amount);

        // Email employee
        if ($employee && $employee->email) {
            \Illuminate\Support\Facades\Mail::send('emails.expense-approved', [
                'employee' => $employee,
                'expense'  => $expense,
            ], function ($m) use ($employee) {
                $m->to($employee->email, $employee->first_name . ' ' . $employee->last_name)
                  ->subject('Expense Claim Approved - ' . tenant('name'));
            });
        }

        return back()->with('success', 'Expense claim approved and employee notified!');
    }

    public function reject(Request $request, ExpenseClaim $expense)
    {
        if ($expense->tenant_id !== tenant('id')) abort(403);
        $expense->update([
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $employee = $expense->employee;
        $employeeName = $employee ? $employee->first_name . ' ' . $employee->last_name : 'Employee';

        // Notify employee
        \App\Services\NotificationService::expenseRejected($employeeName, $expense->claim_number);

        // Email employee
        if ($employee && $employee->email) {
            \Illuminate\Support\Facades\Mail::send('emails.expense-rejected', [
                'employee' => $employee,
                'expense'  => $expense,
            ], function ($m) use ($employee) {
                $m->to($employee->email, $employee->first_name . ' ' . $employee->last_name)
                  ->subject('Expense Claim Update - ' . tenant('name'));
            });
        }

        return back()->with('success', 'Expense claim rejected and employee notified!');
    }

    public function pay(ExpenseClaim $expense)
    {
        if ($expense->tenant_id !== tenant('id')) abort(403);
        if ($expense->status !== 'approved') {
            return back()->with('error', 'Only approved claims can be paid.');
        }

        // Check wallet balance
        $wallet = FacilityWallet::getOrCreate(tenant('id'));
        if (!$wallet->hasSufficientBalance($expense->total_amount)) {
            return back()->with('error', 'Insufficient wallet balance. Please top up your wallet.');
        }

        // Deduct from wallet
        $wallet->debit(
            $expense->total_amount,
            "Expense claim {$expense->claim_number} - {$expense->title}",
            'expense_claim',
            $expense->claim_number,
            auth()->user()->name
        );

        $expense->update(['status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Expense claim paid successfully! KES ' . number_format($expense->total_amount, 2) . ' deducted from wallet.');
    }

    public function destroy(ExpenseClaim $expense)
    {
        if ($expense->tenant_id !== tenant('id')) abort(403);
        $expense->items()->delete();
        $expense->delete();
        return back()->with('success', 'Expense claim deleted!');
    }
}



