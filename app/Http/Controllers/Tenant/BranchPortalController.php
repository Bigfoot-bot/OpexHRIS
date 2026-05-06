<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Tenant\Employee;
use App\Models\Tenant\LeaveRequest;
use App\Models\Tenant\PayrollPeriod;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\Loan;
use App\Models\Tenant\LoanRepayment;
use App\Models\Document;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetAssignment;
use App\Models\Contract;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BranchPortalController extends Controller
{
    public function __construct(private PayrollService $payrollService) {}

    protected function checkAccess(Branch $branch)
    {
        $user = auth()->user();
        if ($branch->tenant_id !== tenant('id')) abort(403);
        if (!$user->is_admin && $user->branch_id !== $branch->id) {
            abort(403, 'You do not have access to this branch.');
        }
    }

    protected function getBranchEmployeeIds(Branch $branch): array
    {
        return Employee::where('tenant_id', tenant('id'))
                       ->where('branch_id', $branch->id)
                       ->pluck('id')->toArray();
    }

    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard(Branch $branch)
    {
        $this->checkAccess($branch);
        $employeeIds  = $this->getBranchEmployeeIds($branch);
        $totalEmp     = count($employeeIds);
        $activeEmp    = Employee::where('branch_id', $branch->id)->where('employment_status', 'active')->count();
        $pendingLeave = LeaveRequest::whereIn('employee_id', $employeeIds)->where('status', 'pending')->count();
        $onLeave      = LeaveRequest::whereIn('employee_id', $employeeIds)->where('status', 'approved')
                            ->where('start_date', '<=', now())->where('end_date', '>=', now())->count();
        $budget       = $branch->budgetAllocation;
        $employees    = Employee::where('branch_id', $branch->id)->latest()->take(5)->get();
        return view('tenant.branch.dashboard', compact('branch', 'totalEmp', 'activeEmp', 'pendingLeave', 'onLeave', 'budget', 'employees'));
    }

    // ─── Employees ────────────────────────────────────────────────────────────

    public function employees(Branch $branch)
    {
        $this->checkAccess($branch);
        $employees = Employee::where('tenant_id', tenant('id'))
                             ->where('branch_id', $branch->id)
                             ->latest()->paginate(15);
        return view('tenant.branch.employees', compact('branch', 'employees'));
    }

    public function createEmployee(Branch $branch)
    {
        $this->checkAccess($branch);
        return view('tenant.branch.employees-create', compact('branch'));
    }

    public function storeEmployee(Request $request, Branch $branch)
    {
        $this->checkAccess($branch);
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email'],
        ]);

        $maxNumber = Employee::where('tenant_id', tenant('id'))
                             ->max(DB::raw('CAST(SUBSTRING(employee_number, 4) AS UNSIGNED)')) ?? 0;
        $employeeNumber = 'EMP' . str_pad($maxNumber + 1, 4, '0', STR_PAD_LEFT);

        while (Employee::where('tenant_id', tenant('id'))->where('employee_number', $employeeNumber)->exists()) {
            $maxNumber++;
            $employeeNumber = 'EMP' . str_pad($maxNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        Employee::create([
            'tenant_id'         => tenant('id'),
            'branch_id'         => $branch->id,
            'employee_number'   => $employeeNumber,
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'job_title'         => $request->job_title,
            'department'        => $request->department,
            'hire_date'         => $request->hire_date,
            'employment_type'   => $request->employment_type ?? 'permanent',
            'employment_status' => 'active',
            'nationality'       => 'Kenyan',
            'license_status'    => 'not_applicable',
        ]);

        return redirect()->route('tenant.branch.employees', $branch)->with('success', 'Employee added successfully!');
    }

    public function editEmployee(Branch $branch, Employee $employee)
    {
        $this->checkAccess($branch);
        if ($employee->branch_id !== $branch->id) abort(403);
        return view('tenant.branch.employees-edit', compact('branch', 'employee'));
    }

    public function updateEmployee(Request $request, Branch $branch, Employee $employee)
    {
        $this->checkAccess($branch);
        if ($employee->branch_id !== $branch->id) abort(403);
        $employee->update($request->only([
            'first_name', 'last_name', 'email', 'phone', 'job_title',
            'department', 'hire_date', 'employment_type', 'employment_status',
        ]));
        return redirect()->route('tenant.branch.employees', $branch)->with('success', 'Employee updated successfully!');
    }

    // ─── Leave ────────────────────────────────────────────────────────────────

    public function leave(Branch $branch)
    {
        $this->checkAccess($branch);
        $employeeIds   = $this->getBranchEmployeeIds($branch);
        $leaveRequests = LeaveRequest::whereIn('employee_id', $employeeIds)
                                     ->with(['employee', 'leaveType'])
                                     ->latest()->paginate(15);
        return view('tenant.branch.leave', compact('branch', 'leaveRequests'));
    }

    public function approveLeave(Branch $branch, LeaveRequest $leaveRequest)
    {
        $this->checkAccess($branch);
        $leaveRequest->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        return back()->with('success', 'Leave request approved!');
    }

    public function rejectLeave(Branch $branch, LeaveRequest $leaveRequest)
    {
        $this->checkAccess($branch);
        $leaveRequest->update(['status' => 'rejected', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        return back()->with('success', 'Leave request rejected!');
    }

    // ─── Payroll ──────────────────────────────────────────────────────────────

    public function payroll(Branch $branch)
    {
        $this->checkAccess($branch);
        $periods = PayrollPeriod::where('tenant_id', tenant('id'))
                                ->where('branch_id', $branch->id)
                                ->latest()->paginate(12);
        return view('tenant.branch.payroll', compact('branch', 'periods'));
    }

    public function payrollCreate(Branch $branch)
    {
        $this->checkAccess($branch);
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->format('F');
        }
        $years = range(date('Y') - 1, date('Y') + 1);
        return view('tenant.branch.payroll-create', compact('branch', 'months', 'years'));
    }

    public function payrollStore(Request $request, Branch $branch)
    {
        $this->checkAccess($branch);
        $validated = $request->validate([
            'month'        => ['required', 'integer', 'between:1,12'],
            'year'         => ['required', 'integer'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $month = $validated['month'];
        $year  = $validated['year'];

        $exists = PayrollPeriod::where('tenant_id', tenant('id'))
                               ->where('branch_id', $branch->id)
                               ->where('month', $month)
                               ->where('year', $year)
                               ->first();

        if ($exists) {
            return redirect()->route('tenant.branch.payroll.show', [$branch, $exists])
                             ->with('error', "Payroll for " . Carbon::create($year, $month, 1)->format('F Y') . " already generated.");
        }

        $period = PayrollPeriod::create([
            'tenant_id'    => tenant('id'),
            'branch_id'    => $branch->id,
            'name'         => $branch->name . ' — ' . Carbon::create($year, $month, 1)->format('F Y'),
            'month'        => $month,
            'year'         => $year,
            'start_date'   => Carbon::create($year, $month, 1)->startOfMonth(),
            'end_date'     => Carbon::create($year, $month, 1)->endOfMonth(),
            'payment_date' => $validated['payment_date'] ?? null,
            'status'       => 'draft',
        ]);

        $employees = Employee::where('tenant_id', tenant('id'))
                             ->where('branch_id', $branch->id)
                             ->where('employment_status', 'active')
                             ->get();

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

        return redirect()->route('tenant.branch.payroll.show', [$branch, $period])
                         ->with('success', "Payroll generated for {$employees->count()} employee(s).");
    }

    public function payrollShow(Branch $branch, PayrollPeriod $period)
    {
        $this->checkAccess($branch);
        if ($period->branch_id !== $branch->id) abort(403);
        $period->load('records.employee');
        return view('tenant.branch.payroll-show', compact('branch', 'period'));
    }

    public function payrollApprove(Branch $branch, PayrollPeriod $period)
    {
        $this->checkAccess($branch);
        if ($period->branch_id !== $branch->id) abort(403);
        if ($period->status !== 'draft') return back()->with('error', 'Payroll already approved.');

        $period->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Payroll approved successfully!');
    }

    public function payrollUpdateRecord(Request $request, Branch $branch, PayrollRecord $record)
    {
        $this->checkAccess($branch);
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
            basicSalary:        (float) $record->basic_salary,
            houseAllowance:     (float) ($validated['house_allowance'] ?? 0),
            transportAllowance: (float) ($validated['transport_allowance'] ?? 0),
            medicalAllowance:   (float) ($validated['medical_allowance'] ?? 0),
            otherAllowances:    (float) ($validated['other_allowances'] ?? 0),
            overtimePay:        (float) ($validated['overtime_pay'] ?? 0),
            loanDeduction:      (float) ($validated['loan_deduction'] ?? 0),
            otherDeductions:    (float) ($validated['other_deductions'] ?? 0),
        );

        $record->update($calculated);
        return back()->with('success', 'Record updated successfully!');
    }

    // ─── Documents ────────────────────────────────────────────────────────────

    public function documents(Branch $branch)
    {
        $this->checkAccess($branch);
        $documents = Document::where('tenant_id', tenant('id'))
                             ->where('visibility', 'all')
                             ->latest()->paginate(15);
        return view('tenant.branch.documents', compact('branch', 'documents'));
    }

    public function documentsStore(Request $request, Branch $branch)
    {
        $this->checkAccess($branch);
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file'  => ['required', 'file', 'max:10240'],
        ]);

        $file     = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        Storage::disk('local')->putFileAs('documents/' . tenant('id'), $file, $filename);

        Document::create([
            'tenant_id'   => tenant('id'),
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => 'documents/' . tenant('id') . '/' . $filename,
            'file_name'   => $file->getClientOriginalName(),
            'file_type'   => $file->getClientOriginalExtension(),
            'file_size'   => $file->getSize(),
            'visibility'  => 'all',
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Document uploaded successfully!');
    }

    public function documentsDownload(Branch $branch, Document $document)
    {
        $this->checkAccess($branch);
        if ($document->tenant_id !== tenant('id')) abort(403);

        if (Storage::disk('local')->exists($document->file_path)) {
            return Storage::disk('local')->download($document->file_path, $document->file_name);
        }
        $publicPath = public_path($document->file_path);
        if (file_exists($publicPath)) {
            return response()->download($publicPath, $document->file_name);
        }
        abort(404);
    }

    public function documentsDestroy(Branch $branch, Document $document)
    {
        $this->checkAccess($branch);
        if ($document->tenant_id !== tenant('id')) abort(403);
        if ($document->uploaded_by !== auth()->id() && !auth()->user()->is_admin) abort(403);

        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }
        $document->delete();
        return back()->with('success', 'Document deleted successfully!');
    }

    // ─── Assets ───────────────────────────────────────────────────────────────

    public function assets(Branch $branch)
    {
        $this->checkAccess($branch);
        $employeeIds = $this->getBranchEmployeeIds($branch);
        $assignedAssetIds = AssetAssignment::whereIn('employee_id', $employeeIds)
                                           ->where('status', 'active')
                                           ->pluck('asset_id');
        $assets = Asset::where('tenant_id', tenant('id'))
                       ->with(['currentAssignment', 'assetCategory'])
                       ->where(function ($q) use ($assignedAssetIds) {
                           $q->whereIn('id', $assignedAssetIds)->orWhere('status', 'available');
                       })
                       ->latest()->paginate(15);
        return view('tenant.branch.assets', compact('branch', 'assets'));
    }

    public function assetsCreate(Branch $branch)
    {
        $this->checkAccess($branch);
        $categories = AssetCategory::where('tenant_id', tenant('id'))->get();
        return view('tenant.branch.assets-create', compact('branch', 'categories'));
    }

    public function assetsStore(Request $request, Branch $branch)
    {
        $this->checkAccess($branch);
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $assetCode = 'AST-' . strtoupper(substr(tenant('id'), 0, 4)) . '-'
            . str_pad(Asset::where('tenant_id', tenant('id'))->count() + 1, 4, '0', STR_PAD_LEFT);

        Asset::create([
            'tenant_id'         => tenant('id'),
            'asset_category_id' => $request->asset_category_id,
            'asset_code'        => $assetCode,
            'name'              => $request->name,
            'description'       => $request->description,
            'serial_number'     => $request->serial_number,
            'brand'             => $request->brand,
            'model'             => $request->model,
            'purchase_price'    => $request->purchase_price,
            'purchase_date'     => $request->purchase_date,
            'status'            => 'available',
            'location'          => $request->location,
            'notes'             => $request->notes,
        ]);

        return redirect()->route('tenant.branch.assets', $branch)->with('success', 'Asset added successfully!');
    }

    public function assetsShow(Branch $branch, Asset $asset)
    {
        $this->checkAccess($branch);
        if ($asset->tenant_id !== tenant('id')) abort(403);
        $assignments = AssetAssignment::where('asset_id', $asset->id)->with('employee')->latest()->get();
        $employees   = Employee::where('tenant_id', tenant('id'))->where('branch_id', $branch->id)->get();
        return view('tenant.branch.assets-show', compact('branch', 'asset', 'assignments', 'employees'));
    }

    public function assetsAssign(Request $request, Branch $branch, Asset $asset)
    {
        $this->checkAccess($branch);
        if ($asset->tenant_id !== tenant('id')) abort(403);
        $request->validate([
            'employee_id'   => ['required', 'exists:employees,id'],
            'assigned_date' => ['required', 'date'],
        ]);

        AssetAssignment::where('asset_id', $asset->id)->where('status', 'active')
                       ->update(['status' => 'returned', 'return_date' => now()]);

        AssetAssignment::create([
            'tenant_id'     => tenant('id'),
            'asset_id'      => $asset->id,
            'employee_id'   => $request->employee_id,
            'assigned_date' => $request->assigned_date,
            'status'        => 'active',
            'notes'         => $request->notes,
            'assigned_by'   => auth()->id(),
        ]);

        $asset->update(['status' => 'assigned']);
        return back()->with('success', 'Asset assigned successfully!');
    }

    public function assetsReturn(Branch $branch, Asset $asset)
    {
        $this->checkAccess($branch);
        if ($asset->tenant_id !== tenant('id')) abort(403);

        AssetAssignment::where('asset_id', $asset->id)->where('status', 'active')
                       ->update(['status' => 'returned', 'return_date' => now()]);
        $asset->update(['status' => 'available']);
        return back()->with('success', 'Asset returned successfully!');
    }

    // ─── Contracts ────────────────────────────────────────────────────────────

    public function contracts(Branch $branch)
    {
        $this->checkAccess($branch);
        $employeeIds = $this->getBranchEmployeeIds($branch);

        Contract::where('tenant_id', tenant('id'))
                ->whereIn('employee_id', $employeeIds)
                ->where('status', 'active')
                ->whereNotNull('end_date')
                ->where('end_date', '<', now())
                ->update(['status' => 'expired']);

        $contracts = Contract::where('tenant_id', tenant('id'))
                             ->whereIn('employee_id', $employeeIds)
                             ->with('employee')->latest()->paginate(15);

        return view('tenant.branch.contracts', compact('branch', 'contracts'));
    }

    public function contractsCreate(Branch $branch)
    {
        $this->checkAccess($branch);
        $employees = Employee::where('tenant_id', tenant('id'))->where('branch_id', $branch->id)->get();
        return view('tenant.branch.contracts-create', compact('branch', 'employees'));
    }

    public function contractsStore(Request $request, Branch $branch)
    {
        $this->checkAccess($branch);
        $request->validate([
            'employee_id'   => ['required', 'exists:employees,id'],
            'title'         => ['required', 'string', 'max:255'],
            'contract_type' => ['required', 'in:permanent,fixed_term,casual,internship,consultant'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['nullable', 'date', 'after:start_date'],
        ]);

        $data = [
            'tenant_id'     => tenant('id'),
            'employee_id'   => $request->employee_id,
            'title'         => $request->title,
            'contract_type' => $request->contract_type,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'salary'        => $request->salary,
            'department'    => $request->department,
            'job_title'     => $request->job_title,
            'status'        => 'active',
            'notes'         => $request->notes,
            'created_by'    => auth()->id(),
        ];

        if ($request->hasFile('contract_file')) {
            $file     = $request->file('contract_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            Storage::disk('local')->putFileAs('contracts/' . tenant('id'), $file, $filename);
            $data['file_path'] = 'contracts/' . tenant('id') . '/' . $filename;
            $data['file_name'] = $file->getClientOriginalName();
        }

        Contract::create($data);
        return redirect()->route('tenant.branch.contracts', $branch)->with('success', 'Contract created successfully!');
    }

    public function contractsShow(Branch $branch, Contract $contract)
    {
        $this->checkAccess($branch);
        if ($contract->tenant_id !== tenant('id')) abort(403);
        $contract->load('employee');
        return view('tenant.branch.contracts-show', compact('branch', 'contract'));
    }

    public function contractsDownload(Branch $branch, Contract $contract)
    {
        $this->checkAccess($branch);
        if ($contract->tenant_id !== tenant('id')) abort(403);
        if (!$contract->file_path) abort(404);

        if (Storage::disk('local')->exists($contract->file_path)) {
            return Storage::disk('local')->download($contract->file_path, $contract->file_name ?? basename($contract->file_path));
        }
        $publicPath = public_path($contract->file_path);
        if (file_exists($publicPath)) {
            return response()->download($publicPath, $contract->file_name ?? basename($contract->file_path));
        }
        abort(404);
    }

    public function contractsDestroy(Branch $branch, Contract $contract)
    {
        $this->checkAccess($branch);
        if ($contract->tenant_id !== tenant('id')) abort(403);

        if ($contract->file_path && Storage::disk('local')->exists($contract->file_path)) {
            Storage::disk('local')->delete($contract->file_path);
        }
        $contract->delete();
        return redirect()->route('tenant.branch.contracts', $branch)->with('success', 'Contract deleted!');
    }

    // ─── Loans ────────────────────────────────────────────────────────────────

    public function loansIndex(Branch $branch)
    {
        $this->checkAccess($branch);
        $employeeIds = $this->getBranchEmployeeIds($branch);
        $loans = Loan::with('employee')
                     ->where('tenant_id', tenant('id'))
                     ->whereIn('employee_id', $employeeIds)
                     ->latest()->paginate(15);
        $stats = [
            'pending'         => Loan::where('tenant_id', tenant('id'))->whereIn('employee_id', $employeeIds)->where('status', 'pending')->count(),
            'active'          => Loan::where('tenant_id', tenant('id'))->whereIn('employee_id', $employeeIds)->where('status', 'active')->count(),
            'total_disbursed' => Loan::where('tenant_id', tenant('id'))->whereIn('employee_id', $employeeIds)->whereIn('status', ['active', 'completed'])->sum('amount'),
            'total_balance'   => Loan::where('tenant_id', tenant('id'))->whereIn('employee_id', $employeeIds)->where('status', 'active')->sum('balance'),
        ];
        return view('tenant.branch.loans', compact('branch', 'loans', 'stats'));
    }

    public function loansCreate(Branch $branch)
    {
        $this->checkAccess($branch);
        $employees = Employee::where('tenant_id', tenant('id'))
                             ->where('branch_id', $branch->id)
                             ->where('employment_status', 'active')
                             ->get();
        return view('tenant.branch.loans-create', compact('branch', 'employees'));
    }

    public function loansStore(Request $request, Branch $branch)
    {
        $this->checkAccess($branch);
        $request->validate([
            'employee_id'      => ['required', 'exists:employees,id'],
            'type'             => ['required'],
            'amount'           => ['required', 'numeric', 'min:1'],
            'interest_rate'    => ['nullable', 'numeric', 'min:0'],
            'repayment_months' => ['required', 'integer', 'min:1'],
            'purpose'          => ['required', 'string'],
        ]);

        $amount         = $request->amount;
        $rate           = $request->interest_rate ?? 0;
        $months         = $request->repayment_months;
        $totalRepayable = $amount + ($amount * $rate / 100);
        $monthly        = round($totalRepayable / $months, 2);

        $count      = Loan::where('tenant_id', tenant('id'))->count() + 1;
        $loanNumber = 'LN-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        Loan::create([
            'tenant_id'         => tenant('id'),
            'employee_id'       => $request->employee_id,
            'loan_number'       => $loanNumber,
            'type'              => $request->type,
            'amount'            => $amount,
            'interest_rate'     => $rate,
            'repayment_months'  => $months,
            'monthly_deduction' => $monthly,
            'total_repayable'   => $totalRepayable,
            'balance'           => $totalRepayable,
            'purpose'           => $request->purpose,
            'status'            => 'pending',
        ]);

        return redirect()->route('tenant.branch.loans.index', $branch)->with('success', 'Loan created successfully!');
    }

    public function loansShow(Branch $branch, Loan $loan)
    {
        $this->checkAccess($branch);
        if ($loan->tenant_id !== tenant('id')) abort(403);
        $loan->load(['employee', 'repayments']);
        return view('tenant.branch.loans-show', compact('branch', 'loan'));
    }

    public function loansApprove(Branch $branch, Loan $loan)
    {
        $this->checkAccess($branch);
        if ($loan->tenant_id !== tenant('id')) abort(403);
        $loan->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        return back()->with('success', 'Loan approved!');
    }

    public function loansReject(Branch $branch, Loan $loan)
    {
        $this->checkAccess($branch);
        if ($loan->tenant_id !== tenant('id')) abort(403);
        $loan->update(['status' => 'rejected']);
        return back()->with('success', 'Loan rejected!');
    }

    public function loansDisburse(Branch $branch, Loan $loan)
    {
        $this->checkAccess($branch);
        if ($loan->tenant_id !== tenant('id')) abort(403);
        if ($loan->status !== 'approved') return back()->with('error', 'Only approved loans can be disbursed.');

        $startRepaymentDate = now()->addMonth();

        $loan->update([
            'status'               => 'active',
            'disbursement_date'    => now(),
            'start_repayment_date' => $startRepaymentDate,
        ]);

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

        return back()->with('success', 'Loan disbursed! Repayment schedule generated.');
    }

    public function loansRecordPayment(Request $request, Branch $branch, LoanRepayment $repayment)
    {
        $this->checkAccess($branch);
        $request->validate(['paid_date' => ['required', 'date']]);
        if ($repayment->tenant_id !== tenant('id')) abort(403);

        $repayment->update([
            'status'         => 'paid',
            'paid_date'      => $request->paid_date,
            'payment_method' => $request->payment_method ?? 'payroll_deduction',
        ]);

        $loan       = $repayment->loan;
        $newBalance = $loan->balance - $repayment->amount;
        $status     = $newBalance <= 0 ? 'completed' : 'active';
        $loan->update(['balance' => max(0, $newBalance), 'status' => $status]);

        return back()->with('success', 'Payment recorded!');
    }

    // ─── Announcements ────────────────────────────────────────────────────────

    public function announcements(Branch $branch)
    {
        $this->checkAccess($branch);
        $tenantId = tenant('id');

        $announcements = \App\Models\Announcement::where('tenant_id', $tenantId)
                            ->where('type', 'facility')
                            ->where(function ($q) use ($branch, $tenantId) {
                                $q->where(function ($q2) use ($branch) {
                                    $q2->whereNull('employee_id')
                                       ->where(function ($q3) use ($branch) {
                                           $q3->whereNull('branch_id')->orWhere('branch_id', $branch->id);
                                       });
                                })->orWhereIn('id', function ($sub) use ($tenantId) {
                                    $sub->selectRaw('MIN(id)')
                                        ->from('announcements')
                                        ->where('tenant_id', $tenantId)
                                        ->where('type', 'facility')
                                        ->whereNotNull('employee_id')
                                        ->groupByRaw("title, body, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i')");
                                });
                            })
                            ->latest()->paginate(15);

        $branchEmployees = Employee::where('branch_id', $branch->id)
                            ->where('tenant_id', $tenantId)
                            ->where('employment_status', 'active')
                            ->orderBy('first_name')
                            ->get();

        return view('tenant.branch.announcements', compact('branch', 'announcements', 'branchEmployees'));
    }

    public function storeAnnouncement(Request $request, Branch $branch)
    {
        $this->checkAccess($branch);
        $request->validate([
            'title'        => ['required', 'string'],
            'content'      => ['required', 'string'],
            'audience'     => ['required', 'in:branch_only,specific_employees'],
            'employee_ids' => ['required_if:audience,specific_employees', 'array', 'min:1'],
        ]);

        $tenantId = tenant('id');

        if ($request->audience === 'branch_only') {
            \App\Models\Announcement::create([
                'tenant_id'   => $tenantId,
                'branch_id'   => $branch->id,
                'employee_id' => null,
                'title'       => $request->title,
                'body'        => $request->input('content'),
                'type'        => 'facility',
                'sender_type' => 'branch',
            ]);
            return back()->with('success', 'Announcement posted for this branch.');
        }

        $employees = Employee::whereIn('id', $request->employee_ids)
                        ->where('tenant_id', $tenantId)
                        ->where('branch_id', $branch->id)
                        ->get();

        foreach ($employees as $employee) {
            \App\Models\Announcement::create([
                'tenant_id'   => $tenantId,
                'branch_id'   => $branch->id,
                'employee_id' => $employee->id,
                'title'       => $request->title,
                'body'        => $request->input('content'),
                'type'        => 'facility',
                'sender_type' => 'branch',
            ]);
        }

        $count = $employees->count();
        return back()->with('success', "Announcement sent to {$count} " . ($count === 1 ? 'employee' : 'employees') . '.');
    }

    public function destroyAnnouncement(Branch $branch, $announcement)
    {
        $this->checkAccess($branch);
        \App\Models\Announcement::where('id', $announcement)->where('branch_id', $branch->id)->delete();
        return back()->with('success', 'Announcement deleted!');
    }

    // ─── Reports / Settings ───────────────────────────────────────────────────

    public function reports(Branch $branch)
    {
        $this->checkAccess($branch);
        $employeeIds   = $this->getBranchEmployeeIds($branch);
        $totalEmp      = count($employeeIds);
        $activeEmp     = Employee::where('branch_id', $branch->id)->where('employment_status', 'active')->count();
        $onLeave       = \App\Models\Tenant\LeaveRequest::whereIn('employee_id', $employeeIds)->where('status', 'approved')->where('start_date', '<=', now())->where('end_date', '>=', now())->count();
        $pendingLeave  = \App\Models\Tenant\LeaveRequest::whereIn('employee_id', $employeeIds)->where('status', 'pending')->count();
        $totalPayroll  = \App\Models\Tenant\PayrollRecord::whereIn('employee_id', $employeeIds)->sum('net_salary');
        $deptBreakdown = Employee::where('branch_id', $branch->id)->selectRaw('department, count(*) as total')->groupBy('department')->get();
        $typeBreakdown = Employee::where('branch_id', $branch->id)->selectRaw('employment_type, count(*) as total')->groupBy('employment_type')->get();
        $budget        = $branch->budgetAllocation;
        return view('tenant.branch.reports', compact('branch', 'totalEmp', 'activeEmp', 'onLeave', 'pendingLeave', 'totalPayroll', 'deptBreakdown', 'typeBreakdown', 'budget'));
    }

    public function settings(Branch $branch)
    {
        $this->checkAccess($branch);
        return view('tenant.branch.settings', compact('branch'));
    }

    public function updateSettings(Request $request, Branch $branch)
    {
        $this->checkAccess($branch);
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email'],
            'phone'   => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
        ]);
        $branch->update($request->only(['name', 'phone', 'email', 'address', 'notes']));
        return back()->with('success', 'Branch settings updated successfully!');
    }

    // ─── Portal Switch ────────────────────────────────────────────────────────

    public function switch()
    {
        $user   = auth()->user();
        $branch = Branch::where('tenant_id', tenant('id'))->where('id', $user->branch_id)->first();
        if (!$branch) {
            return redirect()->route('tenant.employee.dashboard')->with('error', 'No branch assigned.');
        }
        return redirect()->route('tenant.branch.dashboard', $branch);
    }
}
