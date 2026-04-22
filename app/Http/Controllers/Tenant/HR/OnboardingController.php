<?php

namespace App\Http\Controllers\Tenant\HR;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tenant\Employee;
use App\Models\Tenant\OnboardingChecklist;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function show(Employee $employee)
    {
        $checklist = OnboardingChecklist::where('employee_id', $employee->id)
                        ->orderBy('order')
                        ->get();

        // Auto-generate if empty
        if ($checklist->isEmpty()) {
            OnboardingChecklist::generateDefault($employee);
            $checklist = OnboardingChecklist::where('employee_id', $employee->id)
                            ->orderBy('order')
                            ->get();
        }

        $stats = [
            'total'     => $checklist->count(),
            'completed' => $checklist->where('is_completed', true)->count(),
            'pending'   => $checklist->where('is_completed', false)->count(),
            'percent'   => $checklist->count() > 0
                            ? round(($checklist->where('is_completed', true)->count() / $checklist->count()) * 100)
                            : 0,
        ];

        $byCategory = $checklist->groupBy('category');

        return view('tenant.hr.onboarding.show', compact('employee', 'checklist', 'stats', 'byCategory'));
    }

    public function toggle(Request $request, OnboardingChecklist $item)
    {
        $isCompleted = !$item->is_completed;

        $item->update([
            'is_completed' => $isCompleted,
            'completed_by' => $isCompleted ? auth()->id() : null,
            'completed_at' => $isCompleted ? now() : null,
        ]);

        AuditLog::log(
            $isCompleted ? 'completed' : 'uncompleted',
            'Onboarding',
            "{$item->title} for {$item->employee->full_name}"
        );

        return back()->with('success', $isCompleted ? 'Item marked as complete!' : 'Item marked as pending.');
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'category'    => ['required', 'in:documentation,it_setup,training,introduction,compliance,other'],
        ]);

        $maxOrder = OnboardingChecklist::where('employee_id', $employee->id)->max('order') ?? 0;

        OnboardingChecklist::create([
            'tenant_id'   => tenant('id'),
            'employee_id' => $employee->id,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category'    => $validated['category'],
            'order'       => $maxOrder + 1,
        ]);

        return back()->with('success', 'Checklist item added!');
    }

    public function destroy(OnboardingChecklist $item)
    {
        $item->delete();
        return back()->with('success', 'Item removed.');
    }
}