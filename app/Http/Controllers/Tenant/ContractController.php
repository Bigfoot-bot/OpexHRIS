<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Tenant\Employee;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $isEmployee = !auth()->user()->is_admin && auth()->user()->tenantRoles()->count() === 0;
        $query = Contract::where('tenant_id', tenant('id'))->with('employee');

        if ($isEmployee) {
            $employee = auth()->user()->employee;
            if ($employee) {
                $query->where('employee_id', $employee->id);
            } else {
                $query->whereRaw('1=0');
            }
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        Contract::where('tenant_id', tenant('id'))
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->update(['status' => 'expired']);

        $contracts = $query->latest()->paginate(15);
        $expiring  = Contract::where('tenant_id', tenant('id'))
                        ->where('status', 'active')
                        ->whereNotNull('end_date')
                        ->whereBetween('end_date', [now(), now()->addDays(30)])
                        ->with('employee')->get();

        return view('tenant.contracts.index', compact('contracts', 'expiring'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', tenant('id'))->get();
        return view('tenant.contracts.create', compact('employees'));
    }

    public function store(Request $request)
    {
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
            $file->move(public_path('contracts/' . tenant('id')), $filename);
            $data['file_path'] = 'contracts/' . tenant('id') . '/' . $filename;
            $data['file_name'] = $file->getClientOriginalName();
        }

        Contract::create($data);
        return redirect()->route('tenant.contracts.index')->with('success', 'Contract created successfully!');
    }

    public function show(Contract $contract)
    {
        if ($contract->tenant_id !== tenant('id')) abort(403);
        return view('tenant.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        if ($contract->tenant_id !== tenant('id')) abort(403);
        $employees = Employee::where('tenant_id', tenant('id'))->get();
        return view('tenant.contracts.edit', compact('contract', 'employees'));
    }

    public function update(Request $request, Contract $contract)
    {
        if ($contract->tenant_id !== tenant('id')) abort(403);
        $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'contract_type' => ['required'],
            'start_date'    => ['required', 'date'],
        ]);

        $data = $request->only([
            'title', 'contract_type', 'start_date', 'end_date',
            'salary', 'department', 'job_title', 'status', 'notes',
        ]);

        if ($request->hasFile('contract_file')) {
            $file     = $request->file('contract_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('contracts/' . tenant('id')), $filename);
            $data['file_path'] = 'contracts/' . tenant('id') . '/' . $filename;
            $data['file_name'] = $file->getClientOriginalName();
        }

        $contract->update($data);
        return redirect()->route('tenant.contracts.show', $contract)->with('success', 'Contract updated successfully!');
    }

    public function destroy(Contract $contract)
    {
        if ($contract->tenant_id !== tenant('id')) abort(403);
        $contract->delete();
        return redirect()->route('tenant.contracts.index')->with('success', 'Contract deleted successfully!');
    }
}
