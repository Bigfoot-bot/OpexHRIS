<?php

namespace App\Http\Controllers\Tenant\Training;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
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
        return view('tenant.training.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
            'max_participants' => ['nullable', 'integer'],
        ]);

        $validated['tenant_id'] = tenant('id');
        $validated['status']    = 'planned';

        TrainingProgram::create($validated);

        return redirect()->route('tenant.training.index')
                         ->with('success', 'Training program created successfully!');
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

        // Email employee about enrollment
        try {
            $user = User::where('tenant_id', tenant('id'))
                        ->where('employee_id', $request->employee_id)
                        ->first();
            if ($user) {
                $link = 'http://' . request()->getHost() . '/training/' . $training->id;
                Mail::to($user->email)->send(new TrainingEnrolled($user, $training, $link));
            }
        } catch (\Exception $e) {
            // Silently fail
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
