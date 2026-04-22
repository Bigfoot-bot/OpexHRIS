<?php

namespace App\Http\Controllers\Tenant\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Applicant;
use App\Models\Tenant\JobPosition;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    public function create(JobPosition $position)
    {
        return view('tenant.recruitment.applicants.create', compact('position'));
    }

    public function store(Request $request, JobPosition $position)
    {
        $validated = $request->validate([
            'first_name'            => ['required', 'string'],
            'last_name'             => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'phone'                 => ['nullable', 'string'],
            'current_employer'      => ['nullable', 'string'],
            'current_position'      => ['nullable', 'string'],
            'years_of_experience'   => ['nullable', 'integer'],
            'highest_qualification' => ['nullable', 'string'],
            'cover_letter'          => ['nullable', 'string'],
        ]);

        $validated['tenant_id']       = tenant('id');
        $validated['job_position_id'] = $position->id;
        $validated['stage']           = 'applied';

        Applicant::create($validated);

        return redirect()->route('tenant.positions.show', $position)
                         ->with('success', 'Applicant added successfully!');
    }

    public function show(Applicant $applicant)
    {
        $applicant->load('jobPosition');
        return view('tenant.recruitment.applicants.show', compact('applicant'));
    }

    public function updateStage(Request $request, Applicant $applicant)
    {
        $request->validate([
            'stage' => ['required', 'in:applied,shortlisted,interview,assessment,offer,hired,rejected'],
        ]);

        $applicant->update([
            'stage'          => $request->stage,
            'interview_date' => $request->interview_date,
            'score'          => $request->score,
            'notes'          => $request->notes,
        ]);

        return back()->with('success', 'Applicant stage updated successfully!');
    }

    public function destroy(Applicant $applicant)
    {
        $position = $applicant->jobPosition;
        $applicant->delete();
        return redirect()->route('tenant.positions.show', $position)
                         ->with('success', 'Applicant removed.');
    }
}