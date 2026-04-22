<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee;
use App\Models\Tenant\LeaveRequest;
use App\Models\Tenant\PayrollRecord;
use App\Models\Tenant\TrainingProgram;
use App\Models\Tenant\JobPosition;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return view('tenant.search.index', [
                'query'      => $query,
                'employees'  => collect(),
                'leaves'     => collect(),
                'payroll'    => collect(),
                'training'   => collect(),
                'positions'  => collect(),
            ]);
        }

        $employees = Employee::where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('employee_number', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('job_title', 'like', "%{$query}%")
              ->orWhere('department', 'like', "%{$query}%");
        })->take(5)->get();

        $leaves = LeaveRequest::with(['employee', 'leaveType'])
            ->whereHas('employee', function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%");
            })->orWhereHas('leaveType', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })->take(5)->get();

        $payroll = PayrollRecord::with(['employee', 'payrollPeriod'])
            ->whereHas('employee', function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('employee_number', 'like', "%{$query}%");
            })->take(5)->get();

        $training = TrainingProgram::where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->take(5)->get();

        $positions = JobPosition::where('title', 'like', "%{$query}%")
            ->orWhere('department', 'like', "%{$query}%")
            ->take(5)->get();

        return view('tenant.search.index', compact(
            'query', 'employees', 'leaves', 'payroll', 'training', 'positions'
        ));
    }
}