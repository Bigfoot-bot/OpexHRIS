<?php

namespace App\Http\Controllers\Tenant\Performance;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
use App\Models\Tenant\PerformanceReview;
use App\Models\Tenant\PerformanceGoal;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index()
    {
        $reviews = PerformanceReview::with('employee')
                        ->latest()
                        ->paginate(15);

        $stats = [
            'total_reviews'    => PerformanceReview::count(),
            'completed'        => PerformanceReview::where('status', 'completed')->count(),
            'pending'          => PerformanceReview::whereIn('status', ['draft', 'self_assessment', 'manager_review'])->count(),
            'avg_rating'       => round(PerformanceReview::whereNotNull('final_rating')->avg('final_rating'), 1),
        ];

        return view('tenant.performance.index', compact('reviews', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('employment_status', 'active')->get();
        $years     = range(date('Y') - 1, date('Y') + 1);
        return view('tenant.performance.create', compact('employees', 'years'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'   => ['required', 'exists:employees,id'],
            'review_period' => ['required', 'string'],
            'review_year'   => ['required', 'integer'],
            'review_type'   => ['required', 'in:quarterly,bi_annual,annual,probation'],
            'due_date'      => ['nullable', 'date'],
        ]);

        $validated['tenant_id'] = tenant('id');
        $validated['status']    = 'draft';

        PerformanceReview::create($validated);

        return redirect()->route('tenant.performance.index')
                         ->with('success', 'Performance review created successfully!');
    }

    public function show(PerformanceReview $performance)
    {
        $performance->load('employee');
        $goals = PerformanceGoal::where('employee_id', $performance->employee_id)
                                ->where('year', $performance->review_year)
                                ->get();
        return view('tenant.performance.show', compact('performance', 'goals'));
    }

    public function update(Request $request, PerformanceReview $performance)
    {
        $validated = $request->validate([
            'self_rating'           => ['nullable', 'numeric', 'between:1,5'],
            'manager_rating'        => ['nullable', 'numeric', 'between:1,5'],
            'final_rating'          => ['nullable', 'numeric', 'between:1,5'],
            'self_assessment'       => ['nullable', 'string'],
            'manager_comments'      => ['nullable', 'string'],
            'strengths'             => ['nullable', 'string'],
            'areas_for_improvement' => ['nullable', 'string'],
            'goals_next_period'     => ['nullable', 'string'],
            'status'                => ['nullable', 'in:draft,self_assessment,manager_review,completed'],
            'review_date'           => ['nullable', 'date'],
        ]);

        $performance->update($validated);

        return back()->with('success', 'Performance review updated successfully!');
    }

    public function destroy(PerformanceReview $performance)
    {
        $performance->delete();
        return redirect()->route('tenant.performance.index')
                         ->with('success', 'Performance review deleted.');
    }

    // Goals
    public function storeGoal(Request $request)
    {
        $validated = $request->validate([
            'employee_id'      => ['required', 'exists:employees,id'],
            'title'            => ['required', 'string'],
            'description'      => ['nullable', 'string'],
            'category'         => ['required', 'in:performance,learning,behavioral,project'],
            'weight'           => ['nullable', 'integer', 'between:0,100'],
            'target_value'     => ['nullable', 'numeric'],
            'measurement_unit' => ['nullable', 'string'],
            'start_date'       => ['nullable', 'date'],
            'due_date'         => ['nullable', 'date'],
            'year'             => ['required', 'integer'],
            'quarter'          => ['nullable', 'string'],
        ]);

        $validated['tenant_id'] = tenant('id');
        $validated['status']    = 'not_started';

        PerformanceGoal::create($validated);

        return back()->with('success', 'Goal added successfully!');
    }

    public function updateGoal(Request $request, PerformanceGoal $goal)
    {
        $validated = $request->validate([
            'progress'     => ['required', 'integer', 'between:0,100'],
            'actual_value' => ['nullable', 'numeric'],
            'status'       => ['required', 'in:not_started,in_progress,completed,cancelled'],
        ]);

        if ($validated['status'] === 'completed') {
            $validated['completed_date'] = now();
        }

        $goal->update($validated);

        return back()->with('success', 'Goal updated successfully!');
    }
}