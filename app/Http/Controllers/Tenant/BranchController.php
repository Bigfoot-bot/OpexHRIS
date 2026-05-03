<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Mail\BranchManagerAppointed;
use App\Models\Branch;
use App\Models\BranchBudgetAllocation;
use App\Models\Tenant\Employee;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::where('tenant_id', tenant('id'))
                          ->withCount('employees')
                          ->with(['manager', 'budgetAllocation'])
                          ->latest()->get();
        return view('tenant.branches.index', compact('branches'));
    }

    public function create()
    {
        $employees = User::where('tenant_id', tenant('id'))->get();
        return view('tenant.branches.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
        ]);

        $slug = Str::slug($request->name);
        if (Branch::where('tenant_id', tenant('id'))->where('slug', $slug)->exists()) {
            $slug = $slug . '-' . rand(10, 99);
        }

        $branch = Branch::create([
            'tenant_id'  => tenant('id'),
            'name'       => $request->name,
            'slug'       => $slug,
            'address'    => $request->address,
            'phone'      => $request->phone,
            'email'      => $request->email,
            'manager_id' => $request->manager_id,
            'status'     => 'active',
            'notes'      => $request->notes,
        ]);

        BranchBudgetAllocation::create([
            'tenant_id'        => tenant('id'),
            'branch_id'        => $branch->id,
            'allocated_amount' => $request->allocated_amount ?? 0,
            'used_amount'      => 0,
            'period'           => $request->period ?? date('Y'),
        ]);

        if ($branch->manager_id) {
            $this->applyManagerRole(User::find($branch->manager_id), $branch);
        }

        return redirect()->route('tenant.branches.index')->with('success', 'Branch created successfully!');
    }

    public function show(Branch $branch)
    {
        if ($branch->tenant_id !== tenant('id')) abort(403);
        $employees = Employee::where('tenant_id', tenant('id'))->where('branch_id', $branch->id)->get();
        $allUsers  = User::where('tenant_id', tenant('id'))->get();
        $budget    = $branch->budgetAllocation;
        return view('tenant.branches.show', compact('branch', 'employees', 'allUsers', 'budget'));
    }

    public function edit(Branch $branch)
    {
        if ($branch->tenant_id !== tenant('id')) abort(403);
        $users = User::where('tenant_id', tenant('id'))->get();
        return view('tenant.branches.edit', compact('branch', 'users'));
    }

    public function update(Request $request, Branch $branch)
    {
        if ($branch->tenant_id !== tenant('id')) abort(403);
        $request->validate(['name' => ['required', 'string', 'max:255']]);

        $previousManagerId = $branch->manager_id;
        $branch->update($request->only(['name', 'address', 'phone', 'email', 'manager_id', 'status', 'notes']));

        if ($request->manager_id != $previousManagerId) {
            // Strip the old manager's role/branch
            if ($previousManagerId) {
                $oldManager = User::find($previousManagerId);
                if ($oldManager) {
                    $oldManager->removeRole('Branch Manager');
                    $oldManager->update(['branch_id' => null]);
                }
            }
            // Assign the new manager
            if ($request->manager_id) {
                $this->applyManagerRole(User::find($request->manager_id), $branch);
            }
        }

        if ($request->allocated_amount !== null) {
            BranchBudgetAllocation::updateOrCreate(
                ['tenant_id' => tenant('id'), 'branch_id' => $branch->id],
                ['allocated_amount' => $request->allocated_amount, 'period' => $request->period ?? date('Y')]
            );
        }

        return redirect()->route('tenant.branches.show', $branch)->with('success', 'Branch updated successfully!');
    }

    protected function applyManagerRole(?User $manager, Branch $branch): void
    {
        if (!$manager) return;
        $manager->syncRoles(['Branch Manager']);
        $manager->update(['branch_id' => $branch->id]);
        if ($manager->email) {
            Mail::to($manager->email)->send(new BranchManagerAppointed($manager, $branch, tenant('name')));
        }
    }

    public function destroy(Branch $branch)
    {
        if ($branch->tenant_id !== tenant('id')) abort(403);

        // Strip branch roles and branch_id from all users linked to this branch
        $branchUsers = User::where('tenant_id', tenant('id'))
                           ->where('branch_id', $branch->id)
                           ->get();

        foreach ($branchUsers as $user) {
            $user->removeRole('Branch Manager');
            $user->removeRole('Branch HR');
            $user->update(['branch_id' => null]);
        }

        // Unlink employees from this branch
        Employee::where('tenant_id', tenant('id'))
                ->where('branch_id', $branch->id)
                ->update(['branch_id' => null]);

        $branch->delete();
        return redirect()->route('tenant.branches.index')->with('success', 'Branch deleted successfully!');
    }

    public function portal(Branch $branch)
    {
        if ($branch->tenant_id !== tenant('id')) abort(403);
        $employees     = Employee::where('tenant_id', tenant('id'))->where('branch_id', $branch->id)->get();
        $employeeCount = $employees->count();
        $budget        = $branch->budgetAllocation;
        return view('tenant.branches.portal', compact('branch', 'employees', 'employeeCount', 'budget'));
    }

    public function assignEmployee(Request $request, Branch $branch)
    {
        if ($branch->tenant_id !== tenant('id')) abort(403);
        $request->validate(['employee_id' => ['required', 'exists:employees,id']]);
        Employee::where('id', $request->employee_id)->update(['branch_id' => $branch->id]);
        return back()->with('success', 'Employee assigned to branch successfully!');
    }

    public function removeEmployee(Request $request, Branch $branch)
    {
        if ($branch->tenant_id !== tenant('id')) abort(403);
        $request->validate(['employee_id' => ['required', 'exists:employees,id']]);
        Employee::where('id', $request->employee_id)->update(['branch_id' => null]);
        return back()->with('success', 'Employee removed from branch successfully!');
    }

    public function assignHR(Request $request, Branch $branch)
    {
        if ($branch->tenant_id !== tenant('id')) abort(403);
        $request->validate(['user_id' => ['required', 'exists:tenant_users,id']]);

        $user = User::find($request->user_id);
        $user->update(['branch_id' => $branch->id]);

        if ($request->role === 'branch_manager') {
            $user->syncRoles(['Branch Manager']);
            $branch->update(['manager_id' => $user->id]);
            if ($user->email) {
                Mail::to($user->email)->send(new BranchManagerAppointed($user, $branch, tenant('name')));
            }
        } else {
            $user->assignRole('Branch HR');
        }

        return back()->with('success', 'User assigned to branch successfully!');
    }
}
