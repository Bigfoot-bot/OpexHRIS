<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Exports\EmployeesExport;
use App\Exports\LeaveRequestsExport;
use App\Exports\PayrollExport;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function employees()
    {
        return (new EmployeesExport)->download();
    }

    public function leaveRequests()
    {
        return (new LeaveRequestsExport)->download();
    }

    public function payroll(Request $request)
    {
        return (new PayrollExport($request->period_id))->download();
    }
}