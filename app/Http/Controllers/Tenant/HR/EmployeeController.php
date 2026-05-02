<?php

namespace App\Http\Controllers\Tenant\HR;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FacilitySubscription;
use App\Models\Tenant\Employee;
use App\Models\Tenant\LeaveRequest;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\PerformanceReview;
use App\Models\Tenant\ProfessionalLicense;
use App\Models\Tenant\DisciplinaryCase;
use App\Models\Tenant\TrainingEnrollment;
use App\Models\Tenant\User;
use App\Mail\EmployeeWelcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    private function subscriptionLimit(): array
    {
        $tenantId     = tenant('id');
        $subscription = FacilitySubscription::where('tenant_id', $tenantId)->latest()->first();
        $plan         = $subscription?->plan;
        $maxEmployees = $plan?->max_employees ?? PHP_INT_MAX;
        $current      = Employee::withoutGlobalScopes()->where('tenant_id', $tenantId)->count();

        return [
            'current'      => $current,
            'max'          => $maxEmployees,
            'plan_name'    => $plan?->name ?? 'Current Plan',
            'at_limit'     => $current >= $maxEmployees,
            'remaining'    => max(0, $maxEmployees - $current),
        ];
    }

    public function index(Request $request)
    {
        $branches = \App\Models\Branch::where('tenant_id', tenant('id'))->get();
        $query = Employee::query();

        if ($request->branch_id === 'none') {
            $query->whereNull('branch_id');
        } elseif ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_number', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->department) {
            $query->where('department', $request->department);
        }
        if ($request->status) {
            $query->where('employment_status', $request->status);
        }

        $employees = $query->latest()->paginate(15);
        $limit     = $this->subscriptionLimit();
        return view('tenant.hr.employees.index', compact('employees', 'branches', 'limit'));
    }

    public function create()
    {
        $limit = $this->subscriptionLimit();

        if ($limit['at_limit']) {
            return redirect()->route('tenant.employees.index')
                ->with('error', "You have reached the maximum of {$limit['max']} employees allowed on your {$limit['plan_name']} plan. Please upgrade your subscription to add more employees.");
        }

        $branches = \App\Models\Branch::where('tenant_id', tenant('id'))->get();
        return view('tenant.hr.employees.create', compact('branches', 'limit'));
    }

    public function store(Request $request)
    {
        $limit = $this->subscriptionLimit();

        if ($limit['at_limit']) {
            return back()->with('error',
                "Employee limit reached. Your {$limit['plan_name']} plan allows a maximum of {$limit['max']} employees across all branches. " .
                "You currently have {$limit['current']} employees. Please upgrade your subscription to add more."
            );
        }

        $validated = $request->validate([
            'first_name'                     => ['required', 'string', 'max:255'],
            'middle_name'                    => ['nullable', 'string', 'max:255'],
            'last_name'                      => ['required', 'string', 'max:255'],
            'email'                          => ['nullable', 'email'],
            'phone'                          => ['required', 'string'],
            'gender'                         => ['required', 'in:male,female,other'],
            'date_of_birth'                  => ['nullable', 'date'],
            'national_id'                    => ['nullable', 'string'],
            'department'                     => ['required', 'string'],
            'job_title'                      => ['required', 'string'],
            'employment_type'                => ['required', 'in:permanent,contract,casual,intern'],
            'employment_status'              => ['required', 'in:active,probation,suspended,terminated,resigned'],
            'hire_date'                      => ['required', 'date'],
            'basic_salary'                   => ['nullable', 'numeric'],
            'professional_cadre'             => ['nullable', 'string'],
            'registration_body'              => ['nullable', 'string'],
            'registration_number'            => ['nullable', 'string'],
            'license_expiry_date'            => ['nullable', 'date'],
            'kra_pin'                        => ['nullable', 'string'],
            'nhif_number'                    => ['nullable', 'string'],
            'nssf_number'                    => ['nullable', 'string'],
            'specialty'                      => ['nullable', 'string'],
            'emergency_contact_name'         => ['nullable', 'string'],
            'emergency_contact_phone'        => ['nullable', 'string'],
            'emergency_contact_relationship' => ['nullable', 'string'],
            'bank_name'                      => ['nullable', 'string'],
            'bank_account_number'            => ['nullable', 'string'],
            'bank_branch'                    => ['nullable', 'string'],
            'bank_code'                      => ['nullable', 'string'],
            'branch_id'                      => ['nullable', 'exists:branches,id'],
        ]);

        $count = Employee::withoutGlobalScopes()->where('tenant_id', tenant('id'))->count() + 1;
        $validated['employee_number'] = 'EMP' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['tenant_id'] = tenant('id');

        $employee = Employee::create($validated);

        AuditLog::log('created', 'Employee', "Added employee {$employee->full_name} ({$employee->employee_number})");

        // Auto-allocate leave balances for all active leave types
        try {
            $leaveTypes = \App\Models\Tenant\LeaveType::where('is_active', true)->get();
            foreach ($leaveTypes as $leaveType) {
                \App\Models\Tenant\LeaveBalance::allocate(
                    tenant('id'),
                    $employee->id,
                    $leaveType->id,
                    $leaveType->days_allowed,
                    now()->year
                );
            }
        } catch (\Exception $e) {
            \Log::error('Leave balance allocation failed: ' . $e->getMessage());
        }

        // Create user account and send welcome email if email is provided
        if (!empty($validated['email'])) {
            try {
                $tenantName = tenant('name') ?? 'HRIS Portal';
                $password   = Str::random(10);

                $user = User::firstOrCreate(
                    ['email' => $validated['email'], 'tenant_id' => tenant('id')],
                    [
                        'name'        => $employee->full_name,
                        'password'    => bcrypt($password),
                        'role'        => 'employee',
                        'status'      => 'active',
                        'employee_id' => $employee->id,
                        'tenant_id'   => tenant('id'),
                    ]
                );

                $link = 'https://' . request()->getHost() . '/set-password?email=' . urlencode($user->email);
                Mail::to($user->email)->send(new EmployeeWelcome($user, $tenantName, $link, $password));
            } catch (\Exception $e) {
                // Silently fail
            }
        }

        return redirect()->route('tenant.employees.index')
                         ->with('success', 'Employee added successfully!');
    }

    public function show(Employee $employee)
    {
        $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
                            ->with('leaveType')->latest()->take(5)->get();
        $payrollRecords = PayrollRecord::where('employee_id', $employee->id)
                            ->with('payrollPeriod')->latest()->take(5)->get();
        $performanceReviews = PerformanceReview::where('employee_id', $employee->id)
                            ->latest()->take(3)->get();
        $licenses = ProfessionalLicense::where('employee_id', $employee->id)->get();
        $disciplinaryCases = DisciplinaryCase::where('employee_id', $employee->id)
                            ->latest()->take(3)->get();
        $trainingEnrollments = TrainingEnrollment::where('employee_id', $employee->id)
                            ->with('trainingProgram')->latest()->take(5)->get();

        return view('tenant.hr.employees.show', compact(
            'employee', 'leaveRequests', 'payrollRecords',
            'performanceReviews', 'licenses', 'disciplinaryCases', 'trainingEnrollments'
        ));
    }

    public function edit(Employee $employee)
    {
        $branches = \App\Models\Branch::where('tenant_id', tenant('id'))->get();
        return view('tenant.hr.employees.edit', compact('employee', 'branches'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name'                     => ['required', 'string', 'max:255'],
            'middle_name'                    => ['nullable', 'string', 'max:255'],
            'last_name'                      => ['required', 'string', 'max:255'],
            'email'                          => ['nullable', 'email'],
            'phone'                          => ['required', 'string'],
            'gender'                         => ['required', 'in:male,female,other'],
            'date_of_birth'                  => ['nullable', 'date'],
            'national_id'                    => ['nullable', 'string'],
            'department'                     => ['required', 'string'],
            'job_title'                      => ['required', 'string'],
            'employment_type'                => ['required', 'in:permanent,contract,casual,intern'],
            'employment_status'              => ['required', 'in:active,probation,suspended,terminated,resigned'],
            'hire_date'                      => ['required', 'date'],
            'basic_salary'                   => ['nullable', 'numeric'],
            'professional_cadre'             => ['nullable', 'string'],
            'registration_body'              => ['nullable', 'string'],
            'registration_number'            => ['nullable', 'string'],
            'license_expiry_date'            => ['nullable', 'date'],
            'kra_pin'                        => ['nullable', 'string'],
            'nhif_number'                    => ['nullable', 'string'],
            'nssf_number'                    => ['nullable', 'string'],
            'specialty'                      => ['nullable', 'string'],
            'emergency_contact_name'         => ['nullable', 'string'],
            'emergency_contact_phone'        => ['nullable', 'string'],
            'emergency_contact_relationship' => ['nullable', 'string'],
            'bank_name'                      => ['nullable', 'string'],
            'bank_account_number'            => ['nullable', 'string'],
            'bank_branch'                    => ['nullable', 'string'],
            'bank_code'                      => ['nullable', 'string'],
            'branch_id'                      => ['nullable', 'exists:branches,id'],
        ]);

        $old = $employee->only(['first_name', 'last_name', 'job_title', 'department', 'basic_salary', 'employment_status']);
        $employee->update($validated);
        AuditLog::log('updated', 'Employee', "Updated employee {$employee->full_name}", $old, $validated);

        return redirect()->route('tenant.employees.show', $employee)
                         ->with('success', 'Employee updated successfully!');
    }

    public function transfer(Request $request, Employee $employee)
    {
        $request->validate([
            'branch_id' => ['nullable'],
        ]);

        $oldBranch = $employee->branch_id ? \App\Models\Branch::find($employee->branch_id)?->name : 'Head Office';
        $newBranch = $request->branch_id ? \App\Models\Branch::find($request->branch_id)?->name : 'Head Office';

        $employee->update(['branch_id' => $request->branch_id ?: null]);

        AuditLog::log('updated', 'Employee', "Transferred {$employee->full_name} from {$oldBranch} to {$newBranch}");

        return back()->with('success', "Employee transferred to {$newBranch} successfully!");
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->full_name;
        $employee->delete();
        AuditLog::log('deleted', 'Employee', "Deleted employee {$name}");
        return redirect()->route('tenant.employees.index')
                         ->with('success', 'Employee removed successfully.');
    }
}










