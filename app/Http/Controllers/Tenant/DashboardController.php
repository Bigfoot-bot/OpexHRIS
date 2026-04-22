<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
use App\Models\Tenant\LeaveRequest;
use App\Models\Tenant\PayrollPeriod;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\PerformanceReview;
use App\Models\Tenant\ProfessionalLicense;
use App\Models\Tenant\JobPosition;
use App\Models\Tenant\DisciplinaryCase;
use App\Models\Tenant\TrainingProgram;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = tenant();

        // Update license statuses
        ProfessionalLicense::all()->each->updateStatus();

        $stats = [
            'total_employees'   => Employee::count(),
            'active_employees'  => Employee::where('employment_status', 'active')->count(),
            'on_leave'          => LeaveRequest::where('status', 'approved')
                                    ->where('start_date', '<=', now())
                                    ->where('end_date', '>=', now())
                                    ->count(),
            'expiring_licenses' => ProfessionalLicense::where('status', 'expiring')->count(),
            'expired_licenses'  => ProfessionalLicense::where('status', 'expired')->count(),
            'open_positions'    => JobPosition::where('status', 'open')->count(),
            'pending_leaves'    => LeaveRequest::where('status', 'pending')->count(),
            'open_cases'        => DisciplinaryCase::where('status', 'open')->count(),
            'active_trainings'  => TrainingProgram::where('status', 'ongoing')->count(),
            'pending_reviews'   => PerformanceReview::whereIn('status', ['draft', 'self_assessment', 'manager_review'])->count(),
        ];

        $recentEmployees  = Employee::latest()->take(5)->get();
        $pendingLeaves    = LeaveRequest::with('employee', 'leaveType')
                            ->where('status', 'pending')
                            ->latest()
                            ->take(5)
                            ->get();
        $recentPayroll    = PayrollPeriod::latest()->first();
        $upcomingTraining = TrainingProgram::where('status', 'planned')
                            ->where('start_date', '>=', now())
                            ->orderBy('start_date')
                            ->take(3)
                            ->get();

        // Chart Data
        // 1. Staff by Department
        $byDepartment = Employee::select('department', DB::raw('count(*) as total'))
                            ->groupBy('department')
                            ->get();
        $departmentLabels = $byDepartment->pluck('department')->map(fn($d) => $d ?? 'Unassigned')->toArray();
        $departmentData   = $byDepartment->pluck('total')->toArray();

        // 2. Leave Status Distribution
        $leaveStatus = LeaveRequest::select('status', DB::raw('count(*) as total'))
                            ->groupBy('status')
                            ->get()
                            ->pluck('total', 'status');
        $leaveStatusData = [
            $leaveStatus->get('pending', 0),
            $leaveStatus->get('approved', 0),
            $leaveStatus->get('rejected', 0),
        ];

        // 3. Employment Type Distribution
        $byType = Employee::select('employment_type', DB::raw('count(*) as total'))
                    ->groupBy('employment_type')
                    ->get();
        $employmentTypeLabels = $byType->pluck('employment_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->toArray();
        $employmentTypeData   = $byType->pluck('total')->toArray();

        // 4. Payroll Trend (last 6 months)
        $payrollTrend = PayrollPeriod::with('records')
                            ->latest()
                            ->take(6)
                            ->get()
                            ->reverse();
        $payrollLabels   = $payrollTrend->pluck('name')->toArray();
        $payrollNetData  = $payrollTrend->map(fn($p) => $p->records->sum('net_salary'))->toArray();
        $payrollGrossData = $payrollTrend->map(fn($p) => $p->records->sum('gross_salary'))->toArray();

        return view('tenant.dashboard.index', compact(
            'tenant',
            'stats',
            'recentEmployees',
            'pendingLeaves',
            'recentPayroll',
            'upcomingTraining',
            'departmentLabels',
            'departmentData',
            'leaveStatusData',
            'employmentTypeLabels',
            'employmentTypeData',
            'payrollLabels',
            'payrollNetData',
            'payrollGrossData'
        ));
    }
}