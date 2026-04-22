<?php

namespace App\Http\Controllers\Tenant\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Tenant\JobPosition;
use Illuminate\Http\Request;

class JobPositionController extends Controller
{
    public function index()
    {
        $positions = JobPosition::withCount('applicants')->latest()->paginate(15);

        $stats = [
            'total'  => JobPosition::count(),
            'open'   => JobPosition::where('status', 'open')->count(),
            'closed' => JobPosition::where('status', 'closed')->count(),
            'draft'  => JobPosition::where('status', 'draft')->count(),
        ];

        return view('tenant.recruitment.positions.index', compact('positions', 'stats'));
    }

    public function create()
    {
        return view('tenant.recruitment.positions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string'],
            'department'        => ['required', 'string'],
            'location'          => ['nullable', 'string'],
            'type'              => ['required', 'in:full_time,part_time,contract,intern'],
            'status'            => ['required', 'in:draft,open,closed,on_hold'],
            'description'       => ['nullable', 'string'],
            'requirements'      => ['nullable', 'string'],
            'responsibilities'  => ['nullable', 'string'],
            'salary_min'        => ['nullable', 'numeric'],
            'salary_max'        => ['nullable', 'numeric'],
            'vacancies'         => ['required', 'integer', 'min:1'],
            'closing_date'      => ['nullable', 'date'],
        ]);

        $validated['tenant_id'] = tenant('id');

        JobPosition::create($validated);

        return redirect()->route('tenant.positions.index')
                         ->with('success', 'Job position created successfully!');
    }

    public function show(JobPosition $position)
    {
        $position->load('applicants');
        $stageStats = [
            'applied'     => $position->applicants->where('stage', 'applied')->count(),
            'shortlisted' => $position->applicants->where('stage', 'shortlisted')->count(),
            'interview'   => $position->applicants->where('stage', 'interview')->count(),
            'assessment'  => $position->applicants->where('stage', 'assessment')->count(),
            'offer'       => $position->applicants->where('stage', 'offer')->count(),
            'hired'       => $position->applicants->where('stage', 'hired')->count(),
            'rejected'    => $position->applicants->where('stage', 'rejected')->count(),
        ];
        return view('tenant.recruitment.positions.show', compact('position', 'stageStats'));
    }

    public function edit(JobPosition $position)
    {
        return view('tenant.recruitment.positions.edit', compact('position'));
    }

    public function update(Request $request, JobPosition $position)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string'],
            'department'        => ['required', 'string'],
            'location'          => ['nullable', 'string'],
            'type'              => ['required', 'in:full_time,part_time,contract,intern'],
            'status'            => ['required', 'in:draft,open,closed,on_hold'],
            'description'       => ['nullable', 'string'],
            'requirements'      => ['nullable', 'string'],
            'responsibilities'  => ['nullable', 'string'],
            'salary_min'        => ['nullable', 'numeric'],
            'salary_max'        => ['nullable', 'numeric'],
            'vacancies'         => ['required', 'integer', 'min:1'],
            'closing_date'      => ['nullable', 'date'],
        ]);

        $position->update($validated);

        return redirect()->route('tenant.positions.show', $position)
                         ->with('success', 'Job position updated successfully!');
    }

    public function destroy(JobPosition $position)
    {
        $position->delete();
        return redirect()->route('tenant.positions.index')
                         ->with('success', 'Job position deleted.');
    }
}