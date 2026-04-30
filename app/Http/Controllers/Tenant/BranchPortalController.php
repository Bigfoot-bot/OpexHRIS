<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Tenant\Employee;
use App\Models\Tenant\LeaveRequest;
use App\Models\Document;
use App\Models\Asset;
use App\Models\Contract;
use App\Models\Tenant\PayrollRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchPortalController extends Controller
{
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

    public function documents(Branch $branch)
    {
        $this->checkAccess($branch);
        $documents = Document::where('tenant_id', tenant('id'))
                             ->where('visibility', 'all')
                             ->latest()->paginate(15);
        return view('tenant.branch.documents', compact('branch', 'documents'));
    }

    public function assets(Branch $branch)
    {
        $this->checkAccess($branch);
        $employeeIds = $this->getBranchEmployeeIds($branch);
        $assets = Asset::where('tenant_id', tenant('id'))
                       ->whereHas('currentAssignment', function($q) use ($employeeIds) {
                           $q->whereIn('employee_id', $employeeIds);
                       })
                       ->with(['currentAssignment', 'assetCategory'])
                       ->latest()->paginate(15);
        return view('tenant.branch.assets', compact('branch', 'assets'));
    }

    public function contracts(Branch $branch)
    {
        $this->checkAccess($branch);
        $employeeIds = $this->getBranchEmployeeIds($branch);
        $contracts   = Contract::where('tenant_id', tenant('id'))
                               ->whereIn('employee_id', $employeeIds)
                               ->with('employee')->latest()->paginate(15);
        return view('tenant.branch.contracts', compact('branch', 'contracts'));
    }

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

    public function announcements(Branch $branch)
    {
        $this->checkAccess($branch);
        $tenantId = tenant('id');

        // Show: facility-wide (no branch, no employee), this-branch-only, or employee-targeted (representative row per batch)
        $announcements = \App\Models\Announcement::where('tenant_id', $tenantId)
                            ->where('type', 'facility')
                            ->where(function ($q) use ($branch, $tenantId) {
                                $q->where(function ($q2) use ($branch) {
                                    // Facility-wide or branch-specific, no employee targeting
                                    $q2->whereNull('employee_id')
                                       ->where(function ($q3) use ($branch) {
                                           $q3->whereNull('branch_id')->orWhere('branch_id', $branch->id);
                                       });
                                })->orWhereIn('id', function ($sub) use ($tenantId) {
                                    // Representative row per employee-targeted batch
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

        // All active employees across the facility (for "all branches" option)
        $allEmployees = Employee::where('tenant_id', $tenantId)
                            ->where('employment_status', 'active')
                            ->with('branch')
                            ->orderBy('first_name')
                            ->get();

        return view('tenant.branch.announcements', compact('branch', 'announcements', 'branchEmployees', 'allEmployees'));
    }

    public function storeAnnouncement(Request $request, Branch $branch)
    {
        $this->checkAccess($branch);
        $request->validate([
            'title'        => ['required', 'string'],
            'content'      => ['required', 'string'],
            'audience'     => ['required', 'in:all_branches,branch_only,specific_employees'],
            'employee_ids' => ['required_if:audience,specific_employees', 'array', 'min:1'],
        ]);

        $tenantId = tenant('id');

        if ($request->audience === 'all_branches') {
            // Facility-wide, no branch restriction
            \App\Models\Announcement::create([
                'tenant_id'   => $tenantId,
                'branch_id'   => null,
                'employee_id' => null,
                'title'       => $request->title,
                'body'        => $request->input('content'),
                'type'        => 'facility',
                'sender_type' => 'branch',
            ]);
            return back()->with('success', 'Announcement sent to all employees across all branches.');
        }

        if ($request->audience === 'branch_only') {
            // This branch only
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

        // Specific employees
        $employees = Employee::whereIn('id', $request->employee_ids)
                        ->where('tenant_id', $tenantId)
                        ->get();

        foreach ($employees as $employee) {
            \App\Models\Announcement::create([
                'tenant_id'   => $tenantId,
                'branch_id'   => null,
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

    public function payroll(Branch $branch)
    {
        $this->checkAccess($branch);
        $employeeIds = $this->getBranchEmployeeIds($branch);
        $records     = PayrollRecord::whereIn('employee_id', $employeeIds)
                                    ->with(['employee', 'period'])
                                    ->latest()->paginate(15);
        return view('tenant.branch.payroll', compact('branch', 'records'));
    }
}








