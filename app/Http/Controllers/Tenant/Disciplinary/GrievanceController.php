<?php

namespace App\Http\Controllers\Tenant\Disciplinary;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Grievance;
use App\Models\Tenant\Employee;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class GrievanceController extends Controller
{
    public function index()
    {
        $grievances = Grievance::with('employee')->latest()->paginate(15);

        $stats = [
            'total'    => Grievance::count(),
            'open'     => Grievance::whereIn('status', ['submitted', 'under_review', 'investigation'])->count(),
            'resolved' => Grievance::where('status', 'resolved')->count(),
            'critical' => Grievance::where('priority', 'critical')->count(),
        ];

        return view('tenant.disciplinary.grievances.index', compact('grievances', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('employment_status', 'active')->get();
        return view('tenant.disciplinary.grievances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'    => ['required', 'exists:employees,id'],
            'title'          => ['required', 'string'],
            'description'    => ['required', 'string'],
            'category'       => ['required', 'in:harassment,discrimination,working_conditions,compensation,management,policy,other'],
            'priority'       => ['required', 'in:low,medium,high,critical'],
            'submitted_date' => ['required', 'date'],
        ]);

        $count = Grievance::withoutGlobalScopes()->where('tenant_id', tenant('id'))->count() + 1;
        $validated['grievance_number'] = 'GR' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['tenant_id']        = tenant('id');
        $validated['status']           = 'submitted';

        $grievance = Grievance::create($validated);

        // Send notification
        $employee = Employee::find($validated['employee_id']);
        NotificationService::grievanceFiled($employee->full_name, $grievance->grievance_number);

        return redirect()->route('tenant.grievances.index')
                         ->with('success', 'Grievance filed successfully!');
    }

    public function show(Grievance $grievance)
    {
        $grievance->load('employee');
        return view('tenant.disciplinary.grievances.show', compact('grievance'));
    }

    public function update(Request $request, Grievance $grievance)
    {
        $validated = $request->validate([
            'status'          => ['required', 'in:submitted,under_review,investigation,resolved,closed,escalated'],
            'resolution'      => ['nullable', 'string'],
            'resolution_date' => ['nullable', 'date'],
        ]);

        $grievance->update($validated);

        return back()->with('success', 'Grievance updated successfully!');
    }

    public function destroy(Grievance $grievance)
    {
        $grievance->delete();
        return redirect()->route('tenant.grievances.index')
                         ->with('success', 'Grievance deleted.');
    }
}