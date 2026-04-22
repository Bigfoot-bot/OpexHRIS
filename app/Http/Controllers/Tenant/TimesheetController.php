<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Timesheet;
use App\Models\Tenant\TimesheetEntry;
use App\Models\Tenant\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $query = Timesheet::with('employee')->where('tenant_id', tenant('id'));
        if ($request->status) $query->where('status', $request->status);
        if ($request->employee_id) $query->where('employee_id', $request->employee_id);
        $timesheets = $query->latest()->paginate(15);
        $employees  = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        $stats = [
            'pending'  => Timesheet::where('tenant_id', tenant('id'))->where('status', 'submitted')->count(),
            'approved' => Timesheet::where('tenant_id', tenant('id'))->where('status', 'approved')->count(),
            'total_hours' => Timesheet::where('tenant_id', tenant('id'))->where('status', 'approved')->sum('total_hours'),
        ];
        return view('tenant.timesheets.index', compact('timesheets', 'employees', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();
        return view('tenant.timesheets.create', compact('employees', 'weekStart', 'weekEnd'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'week_start'  => ['required', 'date'],
            'week_end'    => ['required', 'date'],
        ]);

        $timesheet = Timesheet::create([
            'tenant_id'   => tenant('id'),
            'employee_id' => $request->employee_id,
            'week_start'  => $request->week_start,
            'week_end'    => $request->week_end,
            'status'      => 'draft',
            'notes'       => $request->notes,
        ]);

        // Save entries
        $totalHours = 0;
        $regularHours = 0;
        $overtimeHours = 0;

        if ($request->entries) {
            foreach ($request->entries as $entry) {
                if (empty($entry['clock_in']) || empty($entry['clock_out'])) continue;
                $clockIn  = Carbon::createFromTimeString($entry['clock_in']);
                $clockOut = Carbon::createFromTimeString($entry['clock_out']);
                if ($clockOut <= $clockIn) $clockOut->addDay();
                $hours = round($clockIn->diffInMinutes($clockOut) / 60, 2);
                $regular  = min($hours, 8);
                $overtime = max(0, $hours - 8);
                $totalHours    += $hours;
                $regularHours  += $regular;
                $overtimeHours += $overtime;

                TimesheetEntry::create([
                    'tenant_id'    => tenant('id'),
                    'timesheet_id' => $timesheet->id,
                    'date'         => $entry['date'],
                    'clock_in'     => $entry['clock_in'],
                    'clock_out'    => $entry['clock_out'],
                    'hours'        => $hours,
                    'project'      => $entry['project'] ?? null,
                    'description'  => $entry['description'] ?? null,
                    'work_type'    => $entry['work_type'] ?? 'regular',
                ]);
            }
        }

        $timesheet->update([
            'total_hours'    => $totalHours,
            'regular_hours'  => $regularHours,
            'overtime_hours' => $overtimeHours,
        ]);

        return redirect()->route('tenant.timesheets.index')->with('success', 'Timesheet created successfully!');
    }

    public function show(Timesheet $timesheet)
    {
        if ($timesheet->tenant_id !== tenant('id')) abort(403);
        $timesheet->load(['employee', 'entries']);
        return view('tenant.timesheets.show', compact('timesheet'));
    }

    public function submit(Timesheet $timesheet)
    {
        if ($timesheet->tenant_id !== tenant('id')) abort(403);
        $timesheet->update(['status' => 'submitted']);
        return back()->with('success', 'Timesheet submitted for approval!');
    }

    public function approve(Timesheet $timesheet)
    {
        if ($timesheet->tenant_id !== tenant('id')) abort(403);
        $timesheet->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        return back()->with('success', 'Timesheet approved!');
    }

    public function reject(Timesheet $timesheet)
    {
        if ($timesheet->tenant_id !== tenant('id')) abort(403);
        $timesheet->update(['status' => 'rejected', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        return back()->with('success', 'Timesheet rejected!');
    }

    public function destroy(Timesheet $timesheet)
    {
        if ($timesheet->tenant_id !== tenant('id')) abort(403);
        $timesheet->entries()->delete();
        $timesheet->delete();
        return back()->with('success', 'Timesheet deleted!');
    }
}
