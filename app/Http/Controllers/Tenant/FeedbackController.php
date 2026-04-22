<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\FeedbackRequest;
use App\Models\Tenant\FeedbackResponse;
use App\Models\Tenant\Employee;
use App\Models\Tenant\User;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = FeedbackRequest::with('employee')->where('tenant_id', tenant('id'))->latest()->paginate(15);
        $stats = [
            'pending'   => FeedbackRequest::where('tenant_id', tenant('id'))->where('status', 'pending')->count(),
            'progress'  => FeedbackRequest::where('tenant_id', tenant('id'))->where('status', 'in_progress')->count(),
            'completed' => FeedbackRequest::where('tenant_id', tenant('id'))->where('status', 'completed')->count(),
        ];
        return view('tenant.feedback.index', compact('feedbacks', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        $users     = User::where('tenant_id', tenant('id'))->get();
        return view('tenant.feedback.create', compact('employees', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'title'       => ['required', 'string'],
            'type'        => ['required', 'in:peer,manager,subordinate,self,client'],
            'due_date'    => ['nullable', 'date'],
            'reviewers'   => ['required', 'array', 'min:1'],
        ]);

        $feedback = FeedbackRequest::create([
            'tenant_id'       => tenant('id'),
            'employee_id'     => $request->employee_id,
            'requested_by'    => auth()->id(),
            'title'           => $request->title,
            'description'     => $request->description,
            'type'            => $request->type,
            'due_date'        => $request->due_date,
            'status'          => 'pending',
            'total_reviewers' => count($request->reviewers),
        ]);

        foreach ($request->reviewers as $reviewerId) {
            FeedbackResponse::create([
                'tenant_id'           => tenant('id'),
                'feedback_request_id' => $feedback->id,
                'reviewer_id'         => $reviewerId,
                'is_anonymous'        => $request->boolean('is_anonymous'),
                'is_submitted'        => false,
            ]);
        }

        $feedback->update(['status' => 'in_progress']);

        return redirect()->route('tenant.feedback.index')->with('success', '360 Feedback request created!');
    }

    public function show(FeedbackRequest $feedback)
    {
        if ($feedback->tenant_id !== tenant('id')) abort(403);
        $feedback->load(['employee', 'responses.reviewer']);
        $avgRatings = [
            'overall'       => round($feedback->responses->where('is_submitted', true)->avg('rating_overall'), 1),
            'communication' => round($feedback->responses->where('is_submitted', true)->avg('rating_communication'), 1),
            'teamwork'      => round($feedback->responses->where('is_submitted', true)->avg('rating_teamwork'), 1),
            'technical'     => round($feedback->responses->where('is_submitted', true)->avg('rating_technical'), 1),
            'leadership'    => round($feedback->responses->where('is_submitted', true)->avg('rating_leadership'), 1),
        ];
        return view('tenant.feedback.show', compact('feedback', 'avgRatings'));
    }

    public function respond(FeedbackResponse $response)
    {
        if ($response->tenant_id !== tenant('id')) abort(403);
        if ($response->reviewer_id !== auth()->id()) abort(403);
        $feedback = $response->request()->with('employee')->first();
        return view('tenant.feedback.respond', compact('response', 'feedback'));
    }

    public function submitResponse(Request $request, FeedbackResponse $response)
    {
        if ($response->tenant_id !== tenant('id')) abort(403);
        if ($response->reviewer_id !== auth()->id()) abort(403);

        $request->validate([
            'rating_overall'       => ['required', 'integer', 'between:1,5'],
            'rating_communication' => ['required', 'integer', 'between:1,5'],
            'rating_teamwork'      => ['required', 'integer', 'between:1,5'],
            'rating_technical'     => ['required', 'integer', 'between:1,5'],
            'rating_leadership'    => ['required', 'integer', 'between:1,5'],
        ]);

        $response->update([
            'rating_overall'       => $request->rating_overall,
            'rating_communication' => $request->rating_communication,
            'rating_teamwork'      => $request->rating_teamwork,
            'rating_technical'     => $request->rating_technical,
            'rating_leadership'    => $request->rating_leadership,
            'strengths'            => $request->strengths,
            'improvements'         => $request->improvements,
            'comments'             => $request->comments,
            'is_submitted'         => true,
            'submitted_at'         => now(),
        ]);

        // Update feedback request completed count
        $feedbackRequest = $response->request;
        $completed = $feedbackRequest->responses()->where('is_submitted', true)->count();
        $status = $completed >= $feedbackRequest->total_reviewers ? 'completed' : 'in_progress';
        $feedbackRequest->update(['completed_reviews' => $completed, 'status' => $status]);

        return redirect()->route('tenant.feedback.index')->with('success', 'Feedback submitted successfully!');
    }

    public function destroy(FeedbackRequest $feedback)
    {
        if ($feedback->tenant_id !== tenant('id')) abort(403);
        $feedback->responses()->delete();
        $feedback->delete();
        return back()->with('success', 'Feedback request deleted!');
    }
}
