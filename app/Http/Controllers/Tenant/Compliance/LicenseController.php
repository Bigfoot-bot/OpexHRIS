<?php

namespace App\Http\Controllers\Tenant\Compliance;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
use App\Models\Tenant\ProfessionalLicense;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function index()
    {
        // Update all license statuses
        ProfessionalLicense::all()->each->updateStatus();

        $licenses = ProfessionalLicense::with('employee')
                        ->latest()
                        ->paginate(15);

        $stats = [
            'total'    => ProfessionalLicense::count(),
            'valid'    => ProfessionalLicense::where('status', 'valid')->count(),
            'expiring' => ProfessionalLicense::where('status', 'expiring')->count(),
            'expired'  => ProfessionalLicense::where('status', 'expired')->count(),
        ];

        return view('tenant.compliance.licenses.index', compact('licenses', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('employment_status', 'active')->get();
        return view('tenant.compliance.licenses.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'    => ['required', 'exists:employees,id'],
            'license_name'   => ['required', 'string'],
            'license_number' => ['required', 'string'],
            'issuing_body'   => ['required', 'string'],
            'issue_date'     => ['nullable', 'date'],
            'expiry_date'    => ['required', 'date'],
            'notes'          => ['nullable', 'string'],
        ]);

        $validated['tenant_id'] = tenant('id');

        $license = ProfessionalLicense::create($validated);
        $license->updateStatus();

        return redirect()->route('tenant.licenses.index')
                         ->with('success', 'License added successfully!');
    }

    public function edit(ProfessionalLicense $license)
    {
        $employees = Employee::where('employment_status', 'active')->get();
        return view('tenant.compliance.licenses.edit', compact('license', 'employees'));
    }

    public function update(Request $request, ProfessionalLicense $license)
    {
        $validated = $request->validate([
            'employee_id'    => ['required', 'exists:employees,id'],
            'license_name'   => ['required', 'string'],
            'license_number' => ['required', 'string'],
            'issuing_body'   => ['required', 'string'],
            'issue_date'     => ['nullable', 'date'],
            'expiry_date'    => ['required', 'date'],
            'notes'          => ['nullable', 'string'],
        ]);

        $license->update($validated);
        $license->updateStatus();

        return redirect()->route('tenant.licenses.index')
                         ->with('success', 'License updated successfully!');
    }

    public function destroy(ProfessionalLicense $license)
    {
        $license->delete();
        return redirect()->route('tenant.licenses.index')
                         ->with('success', 'License deleted.');
    }
}