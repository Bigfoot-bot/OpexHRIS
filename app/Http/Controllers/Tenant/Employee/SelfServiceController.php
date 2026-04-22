<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Tenant\LeaveRequest;
use App\Models\Tenant\LeaveBalance;
use App\Models\Tenant\Notification;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\TrainingEnrollment;
use App\Models\Tenant\OnboardingChecklist;
use App\Models\Tenant\PerformanceReview;

class SelfServiceController extends Controller
{
    protected function getEmployee()
    {
        return auth()->user()->employee;
    }

    public function dashboard()
    {
        $user     = auth()->user();
        $employee = $this->getEmployee();

        if (!$employee) {
            return view('tenant.employee.no-profile');
        }

        $stats = [
            'leave_pending'    => LeaveRequest::where('employee_id', $employee->id)->where('status', 'pending')->count(),
            'leave_approved'   => LeaveRequest::where('employee_id', $employee->id)->where('status', 'approved')->count(),
            'training'         => TrainingEnrollment::where('employee_id', $employee->id)->where('status', 'completed')->count(),
            'onboarding'       => OnboardingChecklist::where('employee_id', $employee->id)->where('is_completed', true)->count(),
            'onboarding_total' => OnboardingChecklist::where('employee_id', $employee->id)->count(),
        ];

        $recentLeave    = LeaveRequest::where('employee_id', $employee->id)->with('leaveType')->latest()->take(5)->get();
        $recentPayslips = PayrollRecord::where('employee_id', $employee->id)->with('payrollPeriod')->latest()->take(3)->get();
        $recentTraining = TrainingEnrollment::where('employee_id', $employee->id)->with('trainingProgram')->latest()->take(3)->get();
        $latestReview   = PerformanceReview::where('employee_id', $employee->id)->latest()->first();

        return view('tenant.employee.dashboard', compact(
            'user', 'employee', 'stats',
            'recentLeave', 'recentPayslips',
            'recentTraining', 'latestReview'
        ));
    }

    public function leave()
    {
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->route('tenant.employee.dashboard');

        $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
                            ->with('leaveType')->latest()->paginate(10);

        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
                            ->where('year', now()->year)
                            ->with('leaveType')
                            ->get();

        return view('tenant.employee.leave', compact('employee', 'leaveRequests', 'leaveBalances'));
    }

    public function payslips()
    {
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->route('tenant.employee.dashboard');

        $payslips = PayrollRecord::where('employee_id', $employee->id)
                        ->with('payrollPeriod')->latest()->paginate(12);

        return view('tenant.employee.payslips', compact('employee', 'payslips'));
    }

    public function training()
    {
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->route('tenant.employee.dashboard');

        $enrollments = TrainingEnrollment::where('employee_id', $employee->id)
                            ->with('trainingProgram')->latest()->paginate(10);

        return view('tenant.employee.training', compact('employee', 'enrollments'));
    }

    public function profile()
    {
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->route('tenant.employee.dashboard');

        return view('tenant.employee.profile', compact('employee'));
    }

    public function notifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
                            ->latest()->paginate(20);

        Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

        return view('tenant.employee.notifications', compact('notifications'));
    }

    public function onboarding()
    {
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->route('tenant.employee.dashboard');

        $checklist = OnboardingChecklist::where('employee_id', $employee->id)
                        ->orderBy('order')->get();

        if ($checklist->isEmpty()) {
            OnboardingChecklist::generateDefault($employee);
            $checklist = OnboardingChecklist::where('employee_id', $employee->id)
                            ->orderBy('order')->get();
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

        return view('tenant.employee.onboarding', compact('employee', 'checklist', 'stats', 'byCategory'));
    }

    public function performanceReviews()
    {
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->route('tenant.employee.dashboard');

        $reviews = PerformanceReview::where('employee_id', $employee->id)
                        ->latest()->paginate(10);

        return view('tenant.employee.performance', compact('employee', 'reviews'));
    }
}
