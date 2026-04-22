<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Shift;
use App\Models\Tenant\Roster;
use App\Models\Tenant\ShiftAssignment;
use App\Models\Tenant\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts  = Shift::where('tenant_id', tenant('id'))->get();
        $rosters = Roster::where('tenant_id', tenant('id'))->latest()->paginate(10);
        return view('tenant.shifts.index', compact('shifts', 'rosters'));
    }

    public function storeShift(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'start_time' => ['required'],
            'end_time'   => ['required'],
        ]);
        $start    = Carbon::createFromTimeString($request->start_time);
        $end      = Carbon::createFromTimeString($request->end_time);
        if ($end <= $start) $end->addDay();
        $duration = $start->diffInHours($end);

        Shift::create([
            'tenant_id'              => tenant('id'),
            'name'                   => $request->name,
            'code'                   => $request->code,
            'start_time'             => $request->start_time,
            'end_time'               => $request->end_time,
            'duration_hours'         => $duration,
            'is_night_shift'         => $request->boolean('is_night_shift'),
            'night_shift_allowance'  => $request->night_shift_allowance ?? 0,
            'break_duration_minutes' => $request->break_duration_minutes ?? 0,
            'color'                  => $request->color ?? '#064e3b',
        ]);
        return back()->with('success', 'Shift created successfully!');
    }

    public function destroyShift(Shift $shift)
    {
        if ($shift->tenant_id !== tenant('id')) abort(403);
        $shift->delete();
        return back()->with('success', 'Shift deleted!');
    }

    public function createRoster()
    {
        $shifts      = Shift::where('tenant_id', tenant('id'))->where('is_active', true)->get();
        $employees   = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        $departments = Employee::where('tenant_id', tenant('id'))->distinct()->pluck('department')->filter();
        return view('tenant.shifts.create-roster', compact('shifts', 'employees', 'departments'));
    }

    public function storeRoster(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ]);

        $roster = Roster::create([
            'tenant_id'  => tenant('id'),
            'name'       => $request->name,
            'department' => $request->department,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => 'draft',
            'created_by' => auth()->id(),
            'notes'      => $request->notes,
        ]);

        return redirect()->route('tenant.shifts.roster', $roster)->with('success', 'Roster created!');
    }

    public function showRoster(Roster $roster)
    {
        if ($roster->tenant_id !== tenant('id')) abort(403);
        $shifts    = Shift::where('tenant_id', tenant('id'))->where('is_active', true)->get();
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')
                             ->when($roster->department, fn($q) => $q->where('department', $roster->department))
                             ->get();
        $assignments = ShiftAssignment::where('roster_id', $roster->id)
                                      ->with(['employee', 'shift'])
                                      ->get()
                                      ->groupBy(fn($a) => $a->employee_id . '_' . $a->date->format('Y-m-d'));

        // Generate date range
        $dates = [];
        $current = $roster->start_date->copy();
        while ($current <= $roster->end_date) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        return view('tenant.shifts.roster', compact('roster', 'shifts', 'employees', 'assignments', 'dates'));
    }

    public function assignShift(Request $request, Roster $roster)
    {
        if ($roster->tenant_id !== tenant('id')) abort(403);
        $request->validate([
            'employee_id' => ['required'],
            'shift_id'    => ['required'],
            'date'        => ['required', 'date'],
        ]);

        ShiftAssignment::updateOrCreate(
            [
                'roster_id'   => $roster->id,
                'employee_id' => $request->employee_id,
                'date'        => $request->date,
            ],
            [
                'tenant_id'   => tenant('id'),
                'shift_id'    => $request->shift_id,
                'status'      => 'scheduled',
                'assigned_by' => auth()->id(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function publishRoster(Roster $roster)
    {
        if ($roster->tenant_id !== tenant('id')) abort(403);
        $roster->update(['status' => 'published']);
        return back()->with('success', 'Roster published successfully!');
    }

    public function destroyRoster(Roster $roster)
    {
        if ($roster->tenant_id !== tenant('id')) abort(403);
        $roster->assignments()->delete();
        $roster->delete();
        return redirect()->route('tenant.shifts.index')->with('success', 'Roster deleted!');
    }
}

