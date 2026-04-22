<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\OvertimeRequest;
use App\Models\Tenant\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $query = OvertimeRequest::with('employee')->where('tenant_id', tenant('id'));

        if ($request->status) $query->where('status', $request->status);
        if ($request->employee_id) $query->where('employee_id', $request->employee_id);

        $overtimes  = $query->latest()->paginate(15);
        $employees  = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        $stats = [
            'pending'  => OvertimeRequest::where('tenant_id', tenant('id'))->where('status', 'pending')->count(),
            'approved' => OvertimeRequest::where('tenant_id', tenant('id'))->where('status', 'approved')->count(),
            'total_hours' => OvertimeRequest::where('tenant_id', tenant('id'))->where('status', 'approved')->sum('hours'),
            'total_amount' => OvertimeRequest::where('tenant_id', tenant('id'))->where('status', 'approved')->sum('amount'),
        ];

        return view('tenant.overtime.index', compact('overtimes', 'employees', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date'        => ['required', 'date'],
            'start_time'  => ['required'],
            'end_time'    => ['required'],
            'reason'      => ['required', 'string'],
        ]);

        $start = Carbon::createFromTimeString($request->start_time);
        $end   = Carbon::createFromTimeString($request->end_time);
        if ($end <= $start) $end->addDay();
        $hours = round($start->diffInMinutes($end) / 60, 2);

        $employee = Employee::find($request->employee_id);
        $rate     = $request->rate_multiplier ?? 1.5;
        $hourlyRate = $employee->basic_salary ? ($employee->basic_salary / 208) : 0;
        $amount   = $hourlyRate * $hours * $rate;

        OvertimeRequest::create([
            'tenant_id'       => tenant('id'),
            'employee_id'     => $request->employee_id,
            'date'            => $request->date,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'hours'           => $hours,
            'reason'          => $request->reason,
            'rate_multiplier' => $rate,
            'amount'          => $amount,
            'status'          => 'pending',
        ]);

        return back()->with('success', 'Overtime request submitted successfully!');
    }

    public function approve(OvertimeRequest $overtime)
    {
        if ($overtime->tenant_id !== tenant('id')) abort(403);
        $overtime->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Overtime approved!');
    }

    public function reject(Request $request, OvertimeRequest $overtime)
    {
        if ($overtime->tenant_id !== tenant('id')) abort(403);
        $overtime->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'remarks'     => $request->remarks,
        ]);
        return back()->with('success', 'Overtime rejected!');
    }

    public function destroy(OvertimeRequest $overtime)
    {
        if ($overtime->tenant_id !== tenant('id')) abort(403);
        $overtime->delete();
        return back()->with('success', 'Overtime request deleted!');
    }
}
