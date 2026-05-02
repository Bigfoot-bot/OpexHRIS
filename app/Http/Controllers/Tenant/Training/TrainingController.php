<?php

namespace App\Http\Controllers\Tenant\Training;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
use App\Models\Tenant\Notification as TenantNotification;
use App\Models\Tenant\TrainingProgram;
use App\Models\Tenant\TrainingEnrollment;
use App\Models\Tenant\User;
use App\Mail\TrainingEnrolled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TrainingController extends Controller
{
    public function index()
    {
        $programs = TrainingProgram::withCount('enrollments')->latest()->paginate(15);

        $stats = [
            'total'     => TrainingProgram::count(),
            'planned'   => TrainingProgram::where('status', 'planned')->count(),
            'ongoing'   => TrainingProgram::where('status', 'ongoing')->count(),
            'completed' => TrainingProgram::where('status', 'completed')->count(),
        ];

        return view('tenant.training.index', compact('programs', 'stats'));
    }

    public function create()
    {
        $employees   = Employee::where('tenant_id', tenant('id'))
                          ->where('employment_status', 'active')
                          ->with('branch')
                          ->orderBy('first_name')
                          ->get();
        $branches    = \App\Models\Branch::where('tenant_id', tenant('id'))->orderBy('name')->get();
        $departments = \App\Models\TenantSetting::get('departments', []);

        return view('tenant.training.create', compact('employees', 'branches', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'            => ['required', 'string'],
            'description'      => ['nullable', 'string'],
            'type'             => ['required', 'in:internal,external,online,conference,workshop'],
            'category'         => ['required', 'in:clinical,administrative,compliance,leadership,technical,soft_skills'],
            'provider'         => ['nullable', 'string'],
            'cpd_points'       => ['nullable', 'integer', 'min:0'],
            'cost'             => ['nullable', 'numeric'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'gte:start_date'],
            'location'         => ['nullable', 'string'],
            'meeting_link'     => ['nullable', 'url'],
            'max_participants' => ['nullable', 'integer'],
            'audience'         => ['required', 'in:all_employees,by_department,specific_employees'],
            'department'       => ['required_if:audience,by_department', 'nullable', 'string'],
            'employee_ids'     => ['required_if:audience,specific_employees', 'array', 'min:1'],
        ]);

        $tenantId = tenant('id');

        $program = TrainingProgram::create([
            'tenant_id'        => $tenantId,
            'status'           => 'planned',
            'title'            => $request->title,
            'description'      => $request->description,
            'type'             => $request->type,
            'category'         => $request->category,
            'provider'         => $request->provider,
            'cpd_points'       => $request->cpd_points,
            'cost'             => $request->cost,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'location'         => $request->location,
            'meeting_link'     => $request->meeting_link,
            'max_participants' => $request->max_participants,
        ]);

        if ($request->audience === 'all_employees') {
            $employees = Employee::where('tenant_id', $tenantId)->where('employment_status', 'active')->get();
        } elseif ($request->audience === 'by_department') {
            $employees = Employee::where('tenant_id', $tenantId)
                            ->where('department', $request->department)
                            ->where('employment_status', 'active')
                            ->get();
        } else {
            $employees = Employee::whereIn('id', $request->employee_ids)->where('tenant_id', $tenantId)->get();
        }

        foreach ($employees as $employee) {
            TrainingEnrollment::create([
                'tenant_id'           => $tenantId,
                'training_program_id' => $program->id,
                'employee_id'         => $employee->id,
                'status'              => 'enrolled',
            ]);

            $user = User::where('tenant_id', $tenantId)->where('employee_id', $employee->id)->first();
            if ($user) {
                TenantNotification::create([
                    'tenant_id' => $tenantId,
                    'user_id'   => $user->id,
                    'title'     => 'Training Enrollment',
                    'message'   => 'You have been enrolled in: ' . $program->title,
                    'type'      => 'info',
                    'link'      => route('tenant.employee.training'),
                ]);

                try {
                    $link = 'https://' . request()->getHost() . '/my/training';
                    Mail::to($user->email)->send(new TrainingEnrolled($user, $program, $link));
                } catch (\Exception $e) {}
            }
        }

        $count = $employees->count();
        return redirect()->route('tenant.training.index')
                         ->with('success', "Training program created and {$count} " . ($count === 1 ? 'employee' : 'employees') . ' enrolled.');
    }

    public function show(TrainingProgram $training)
    {
        $training->load('enrollments.employee');
        $employees = Employee::where('employment_status', 'active')->get();
        return view('tenant.training.show', compact('training', 'employees'));
    }

    public function update(Request $request, TrainingProgram $training)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:planned,ongoing,completed,cancelled'],
        ]);

        $training->update($validated);

        return back()->with('success', 'Training status updated successfully!');
    }

    public function destroy(TrainingProgram $training)
    {
        $training->delete();
        return redirect()->route('tenant.training.index')
                         ->with('success', 'Training program deleted.');
    }

    public function enroll(Request $request, TrainingProgram $training)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
        ]);

        $enrollment = TrainingEnrollment::firstOrCreate([
            'training_program_id' => $training->id,
            'employee_id'         => $request->employee_id,
        ], [
            'tenant_id' => tenant('id'),
            'status'    => 'enrolled',
        ]);

        $user = User::where('tenant_id', tenant('id'))
                    ->where('employee_id', $request->employee_id)
                    ->first();

        if ($user) {
            TenantNotification::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => 'Training Enrollment',
                'message'   => 'You have been enrolled in: ' . $training->title,
                'type'      => 'info',
                'link'      => route('tenant.employee.training'),
            ]);

            try {
                $link = 'https://' . request()->getHost() . '/my/training';
                Mail::to($user->email)->send(new TrainingEnrolled($user, $training, $link));
            } catch (\Exception $e) {}
        }

        return back()->with('success', 'Employee enrolled successfully!');
    }

    public function updateEnrollment(Request $request, TrainingEnrollment $enrollment)
    {
        $validated = $request->validate([
            'status'             => ['required', 'in:enrolled,attended,completed,cancelled'],
            'cpd_points_earned'  => ['nullable', 'integer'],
            'score'              => ['nullable', 'numeric'],
            'completion_date'    => ['nullable', 'date'],
            'certificate_issued' => ['boolean'],
        ]);

        $validated['certificate_issued'] = $request->boolean('certificate_issued');

        $enrollment->update($validated);

        return back()->with('success', 'Enrollment updated successfully!');
    }
}
