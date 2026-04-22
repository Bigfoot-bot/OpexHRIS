<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
use App\Models\Tenant\LeaveRequest;
use App\Models\Tenant\PayrollPeriod;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\TrainingEnrollment;
use App\Models\Tenant\DisciplinaryCase;
use App\Models\Tenant\ProfessionalLicense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('tenant.reports.index');
    }

    public function headcount()
    {
        $stats = [
            'total'      => Employee::count(),
            'active'     => Employee::where('employment_status', 'active')->count(),
            'probation'  => Employee::where('employment_status', 'probation')->count(),
            'suspended'  => Employee::where('employment_status', 'suspended')->count(),
            'terminated' => Employee::where('employment_status', 'terminated')->count(),
            'resigned'   => Employee::where('employment_status', 'resigned')->count(),
        ];

        $byDepartment = Employee::selectRaw('department, count(*) as total')
                            ->groupBy('department')
                            ->orderByDesc('total')
                            ->get();

        $byType = Employee::selectRaw('employment_type, count(*) as total')
                            ->groupBy('employment_type')
                            ->get();

        $byGender = Employee::selectRaw('gender, count(*) as total')
                            ->groupBy('gender')
                            ->get();

        $recentHires = Employee::where('hire_date', '>=', now()->subDays(90))
                            ->orderByDesc('hire_date')
                            ->get();

        return view('tenant.reports.headcount', compact(
            'stats', 'byDepartment', 'byType', 'byGender', 'recentHires'
        ));
    }

    public function payroll()
    {
        $periods = PayrollPeriod::with('records')->latest()->take(12)->get();

        $currentPeriod = PayrollPeriod::latest()->first();

        $stats = [
            'total_gross'      => PayrollRecord::sum('gross_salary'),
            'total_net'        => PayrollRecord::sum('net_salary'),
            'total_paye'       => PayrollRecord::sum('paye'),
            'total_nhif'       => PayrollRecord::sum('nhif'),
            'total_nssf'       => PayrollRecord::sum('nssf_employee'),
            'total_housing'    => PayrollRecord::sum('housing_levy'),
            'avg_salary'       => PayrollRecord::avg('basic_salary'),
        ];

        $topEarners = PayrollRecord::with('employee')
                        ->where('payroll_period_id', optional($currentPeriod)->id)
                        ->orderByDesc('net_salary')
                        ->take(10)
                        ->get();

        return view('tenant.reports.payroll', compact('periods', 'stats', 'topEarners', 'currentPeriod'));
    }

    public function leave()
    {
        $stats = [
            'total'    => LeaveRequest::count(),
            'pending'  => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        $byType = LeaveRequest::selectRaw('leave_type_id, count(*) as total, sum(days_requested) as total_days')
                        ->with('leaveType')
                        ->groupBy('leave_type_id')
                        ->get();

        $byMonth = LeaveRequest::selectRaw('MONTH(start_date) as month, YEAR(start_date) as year, count(*) as total')
                        ->where('start_date', '>=', now()->subYear())
                        ->groupBy('month', 'year')
                        ->orderBy('year')
                        ->orderBy('month')
                        ->get();

        $topLeaves = Employee::withCount(['leaveRequests' => function($q) {
                            $q->where('status', 'approved');
                        }])
                        ->orderByDesc('leave_requests_count')
                        ->take(10)
                        ->get();

        return view('tenant.reports.leave', compact('stats', 'byType', 'byMonth', 'topLeaves'));
    }

    public function compliance()
    {
        ProfessionalLicense::all()->each->updateStatus();

        $stats = [
            'total'    => ProfessionalLicense::count(),
            'valid'    => ProfessionalLicense::where('status', 'valid')->count(),
            'expiring' => ProfessionalLicense::where('status', 'expiring')->count(),
            'expired'  => ProfessionalLicense::where('status', 'expired')->count(),
        ];

        $expiring = ProfessionalLicense::with('employee')
                        ->where('status', 'expiring')
                        ->orderBy('expiry_date')
                        ->get();

        $expired = ProfessionalLicense::with('employee')
                        ->where('status', 'expired')
                        ->orderBy('expiry_date')
                        ->get();

        $disciplinary = [
            'total'  => DisciplinaryCase::count(),
            'open'   => DisciplinaryCase::where('status', 'open')->count(),
            'closed' => DisciplinaryCase::where('status', 'closed')->count(),
        ];

        return view('tenant.reports.compliance', compact('stats', 'expiring', 'expired', 'disciplinary'));
    }

    public function training()
    {
        $stats = [
            'total_programs'     => \App\Models\Tenant\TrainingProgram::count(),
            'total_enrollments'  => TrainingEnrollment::count(),
            'completed'          => TrainingEnrollment::where('status', 'completed')->count(),
            'total_cpd_points'   => TrainingEnrollment::where('status', 'completed')->sum('cpd_points_earned'),
        ];

        $topTrainees = Employee::withCount('trainingEnrollments')
                        ->orderByDesc('training_enrollments_count')
                        ->take(10)
                        ->get();

        return view('tenant.reports.training', compact('stats', 'topTrainees'));
    }
}