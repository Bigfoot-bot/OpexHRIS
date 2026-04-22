<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Http\Controllers\Controller;
use App\Models\Tenant\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::latest()->get();
        return view('tenant.leave.types.index', compact('leaveTypes'));
    }

    public function create()
    {
        return view('tenant.leave.types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'code'                  => ['nullable', 'string', 'max:50'],
            'description'           => ['nullable', 'string'],
            'days_allowed'          => ['required', 'integer', 'min:0'],
            'is_paid'               => ['boolean'],
            'requires_document'     => ['boolean'],
            'allow_half_day'        => ['boolean'],
            'carry_forward'         => ['boolean'],
            'max_carry_forward_days'=> ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_paid']           = $request->boolean('is_paid');
        $validated['requires_document'] = $request->boolean('requires_document');
        $validated['allow_half_day']    = $request->boolean('allow_half_day');
        $validated['carry_forward']     = $request->boolean('carry_forward');
        $validated['tenant_id']         = tenant('id');

        LeaveType::create($validated);

        return redirect()->route('tenant.leave-types.index')
                         ->with('success', 'Leave type created successfully!');
    }

    public function edit(LeaveType $leaveType)
    {
        return view('tenant.leave.types.edit', compact('leaveType'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'code'                  => ['nullable', 'string', 'max:50'],
            'description'           => ['nullable', 'string'],
            'days_allowed'          => ['required', 'integer', 'min:0'],
            'is_paid'               => ['boolean'],
            'requires_document'     => ['boolean'],
            'allow_half_day'        => ['boolean'],
            'carry_forward'         => ['boolean'],
            'max_carry_forward_days'=> ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_paid']           = $request->boolean('is_paid');
        $validated['requires_document'] = $request->boolean('requires_document');
        $validated['allow_half_day']    = $request->boolean('allow_half_day');
        $validated['carry_forward']     = $request->boolean('carry_forward');

        $leaveType->update($validated);

        return redirect()->route('tenant.leave-types.index')
                         ->with('success', 'Leave type updated successfully!');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->route('tenant.leave-types.index')
                         ->with('success', 'Leave type deleted successfully!');
    }
}