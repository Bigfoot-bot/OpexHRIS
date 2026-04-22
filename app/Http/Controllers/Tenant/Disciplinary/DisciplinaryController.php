<?php

namespace App\Http\Controllers\Tenant\Disciplinary;

use App\Http\Controllers\Controller;
use App\Models\Tenant\DisciplinaryCase;
use App\Models\Tenant\Employee;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class DisciplinaryController extends Controller
{
    public function index()
    {
        $cases = DisciplinaryCase::with('employee')->latest()->paginate(15);

        $stats = [
            'total'       => DisciplinaryCase::count(),
            'open'        => DisciplinaryCase::where('status', 'open')->count(),
            'closed'      => DisciplinaryCase::where('status', 'closed')->count(),
            'this_month'  => DisciplinaryCase::whereMonth('created_at', now()->month)->count(),
        ];

        return view('tenant.disciplinary.index', compact('cases', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('employment_status', 'active')->get();
        return view('tenant.disciplinary.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'   => ['required', 'exists:employees,id'],
            'title'         => ['required', 'string'],
            'description'   => ['required', 'string'],
            'type'          => ['required', 'in:verbal_warning,written_warning,final_warning,suspension,termination,other'],
            'severity'      => ['required', 'in:minor,moderate,serious,gross_misconduct'],
            'incident_date' => ['required', 'date'],
            'hearing_date'  => ['nullable', 'date'],
        ]);

        $count = DisciplinaryCase::withoutGlobalScopes()->where('tenant_id', tenant('id'))->count() + 1;
        $validated['case_number'] = 'DC' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['tenant_id']   = tenant('id');
        $validated['status']      = 'open';

        $case = DisciplinaryCase::create($validated);

        // Send notification
        $employee = Employee::find($validated['employee_id']);
        NotificationService::disciplinaryCaseFiled($employee->full_name, $case->case_number);

        return redirect()->route('tenant.disciplinary.index')
                         ->with('success', 'Disciplinary case created successfully!');
    }

    public function show(DisciplinaryCase $disciplinary)
    {
        $disciplinary->load('employee');
        return view('tenant.disciplinary.show', compact('disciplinary'));
    }

    public function update(Request $request, DisciplinaryCase $disciplinary)
    {
        $validated = $request->validate([
            'status'            => ['required', 'in:open,under_investigation,hearing_scheduled,closed,appealed'],
            'outcome'           => ['nullable', 'string'],
            'employee_response' => ['nullable', 'string'],
            'hearing_date'      => ['nullable', 'date'],
            'resolution_date'   => ['nullable', 'date'],
        ]);

        $disciplinary->update($validated);

        return back()->with('success', 'Case updated successfully!');
    }

    public function destroy(DisciplinaryCase $disciplinary)
    {
        $disciplinary->delete();
        return redirect()->route('tenant.disciplinary.index')
                         ->with('success', 'Case deleted.');
    }
}