<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PerformanceImprovementPlan;
use App\Models\Tenant\Employee;
use Illuminate\Http\Request;

class PIPController extends Controller
{
    public function index()
    {
        $pips = PerformanceImprovementPlan::with('employee')->where('tenant_id', tenant('id'))->latest()->paginate(15);
        $stats = [
            'draft'     => PerformanceImprovementPlan::where('tenant_id', tenant('id'))->where('status', 'draft')->count(),
            'active'    => PerformanceImprovementPlan::where('tenant_id', tenant('id'))->where('status', 'active')->count(),
            'completed' => PerformanceImprovementPlan::where('tenant_id', tenant('id'))->where('status', 'completed')->count(),
        ];
        return view('tenant.pip.index', compact('pips', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        return view('tenant.pip.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'title'       => ['required', 'string'],
            'reason'      => ['required', 'string'],
            'goals'       => ['required', 'string'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after:start_date'],
        ]);

        PerformanceImprovementPlan::create([
            'tenant_id'        => tenant('id'),
            'employee_id'      => $request->employee_id,
            'created_by'       => auth()->id(),
            'title'            => $request->title,
            'reason'           => $request->reason,
            'goals'            => $request->goals,
            'support_provided' => $request->support_provided,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'review_date'      => $request->review_date,
            'status'           => 'draft',
        ]);

        return redirect()->route('tenant.pip.index')->with('success', 'PIP created successfully!');
    }

    public function show(PerformanceImprovementPlan $pip)
    {
        if ($pip->tenant_id !== tenant('id')) abort(403);
        $pip->load('employee');
        return view('tenant.pip.show', compact('pip'));
    }

    public function edit(PerformanceImprovementPlan $pip)
    {
        if ($pip->tenant_id !== tenant('id')) abort(403);
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        return view('tenant.pip.edit', compact('pip', 'employees'));
    }

    public function update(Request $request, PerformanceImprovementPlan $pip)
    {
        if ($pip->tenant_id !== tenant('id')) abort(403);
        $request->validate([
            'title'      => ['required', 'string'],
            'status'     => ['required', 'in:draft,active,completed,extended,terminated'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date'],
        ]);

        $pip->update([
            'title'            => $request->title,
            'reason'           => $request->reason,
            'goals'            => $request->goals,
            'support_provided' => $request->support_provided,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'review_date'      => $request->review_date,
            'status'           => $request->status,
            'progress_notes'   => $request->progress_notes,
            'outcome'          => $request->outcome,
            'reviewed_by'      => auth()->id(),
        ]);

        return redirect()->route('tenant.pip.show', $pip)->with('success', 'PIP updated successfully!');
    }

    public function activate(PerformanceImprovementPlan $pip)
    {
        if ($pip->tenant_id !== tenant('id')) abort(403);
        $pip->update(['status' => 'active']);
        return back()->with('success', 'PIP activated!');
    }

    public function complete(PerformanceImprovementPlan $pip)
    {
        if ($pip->tenant_id !== tenant('id')) abort(403);
        $pip->update(['status' => 'completed', 'reviewed_by' => auth()->id()]);
        return back()->with('success', 'PIP marked as completed!');
    }

    public function destroy(PerformanceImprovementPlan $pip)
    {
        if ($pip->tenant_id !== tenant('id')) abort(403);
        $pip->delete();
        return back()->with('success', 'PIP deleted!');
    }
}
