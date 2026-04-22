<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tenant\Employee;
use App\Models\Tenant\LeaveRequest;
use App\Models\Tenant\LeaveType;
use App\Models\Tenant\LeaveBalance;
use App\Models\Tenant\User;
use App\Services\NotificationService;
use App\Mail\LeaveRequestSubmitted;
use App\Mail\LeaveRequestDecided;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $branches = \App\Models\Branch::where('tenant_id', tenant('id'))->get();
        $query = LeaveRequest::with(['employee', 'leaveType']);

        if ($request->branch_id) {
            $employeeIds = \App\Models\Tenant\Employee::where('branch_id', $request->branch_id)->pluck('id');
            $query->whereIn('employee_id', $employeeIds);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->latest()->paginate(15);
        return view('tenant.leave.requests.index', compact('leaveRequests', 'branches'));
    }

    public function create()
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $user       = auth()->user();

        if ($user->employee_id && $user->isInEmployeePortal()) {
            $employees        = Employee::where('id', $user->employee_id)->get();
            $selectedEmployee = $user->employee;
            $fromPortal       = true;
        } else {
            $employees        = Employee::where('employment_status', 'active')->get();
            $selectedEmployee = null;
            $fromPortal       = false;
        }

        return view('tenant.leave.requests.create', compact(
            'employees', 'leaveTypes', 'selectedEmployee', 'fromPortal'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'   => ['required', 'exists:employees,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'gte:start_date'],
            'is_half_day'   => ['boolean'],
            'reason'        => ['nullable', 'string'],
        ]);

        $startDate     = \Carbon\Carbon::parse($validated['start_date']);
        $endDate       = \Carbon\Carbon::parse($validated['end_date']);
        $daysRequested = $request->boolean('is_half_day') ? 0.5 : $startDate->diffInWeekdays($endDate) + 1;

        // Check leave balance
        $balance = LeaveBalance::where('tenant_id', tenant('id'))
                               ->where('employee_id', $validated['employee_id'])
                               ->where('leave_type_id', $validated['leave_type_id'])
                               ->where('year', now()->year)
                               ->first();

        if ($balance && $daysRequested > $balance->remaining_days) {
            return back()->withErrors([
                'leave_type_id' => "Insufficient leave balance. You have {$balance->remaining_days} days remaining for this leave type."
            ])->withInput();
        }

        $validated['days_requested'] = $daysRequested;
        $validated['is_half_day']    = $request->boolean('is_half_day');
        $validated['tenant_id']      = tenant('id');
        $validated['status']         = 'pending';

        $leaveRequest = LeaveRequest::create($validated);
        $employee     = Employee::find($validated['employee_id']);

        NotificationService::leaveRequested($employee->full_name, $daysRequested);
        AuditLog::log('created', 'Leave', "Leave request submitted for {$employee->full_name} ({$daysRequested} days)");

        // Email HR admins about new leave request
        try {
            $tenantId = tenant('id');
            $admins = DB::table('tenant_users')
                        ->where('tenant_id', $tenantId)
                        ->where('is_hr', true)
                        ->whereNull('deleted_at')
                        ->get();
            $link = 'http://' . request()->getHost() . '/leave-requests/' . $leaveRequest->id;
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new LeaveRequestSubmitted($leaveRequest, $link));
            }
        } catch (\Exception $e) {
            \Log::error('Leave submitted email failed: ' . $e->getMessage());
        }

        if ($request->boolean('from_portal')) {
            return redirect()->route('tenant.employee.leave')
                             ->with('success', 'Leave request submitted successfully!');
        }

        return redirect()->route('tenant.leave-requests.index')
                         ->with('success', 'Leave request submitted successfully!');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['employee', 'leaveType']);
        return view('tenant.leave.requests.show', compact('leaveRequest'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Deduct from leave balance
        LeaveBalance::deduct(
            $leaveRequest->tenant_id,
            $leaveRequest->employee_id,
            $leaveRequest->leave_type_id,
            $leaveRequest->days_requested,
            now()->year
        );

        NotificationService::leaveApproved($leaveRequest->employee->full_name);
        AuditLog::log('approved', 'Leave', "Approved leave request for {$leaveRequest->employee->full_name}");

        // Email employee about decision
        try {
            $employee = $leaveRequest->employee;
            $userRecord = DB::table('tenant_users')
                           ->where('tenant_id', $leaveRequest->tenant_id)
                           ->where('employee_id', $employee->id)
                           ->whereNull('deleted_at')
                           ->first();
            if ($userRecord && $userRecord->email) {
                $link = 'http://' . request()->getHost() . '/my/leave';
                Mail::to($userRecord->email)->send(new LeaveRequestDecided($leaveRequest, $link));
            }
        } catch (\Exception $e) {
            \Log::error('Leave approval email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Leave request approved successfully!');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string'],
        ]);

        $leaveRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Restore leave balance if it was previously approved
        if ($leaveRequest->status === 'approved') {
            LeaveBalance::restore(
                $leaveRequest->tenant_id,
                $leaveRequest->employee_id,
                $leaveRequest->leave_type_id,
                $leaveRequest->days_requested,
                now()->year
            );
        }

        NotificationService::leaveRejected($leaveRequest->employee->full_name);
        AuditLog::log('rejected', 'Leave', "Rejected leave request for {$leaveRequest->employee->full_name}");

        // Email employee about decision
        try {
            $employee = $leaveRequest->employee;
            $userRecord = DB::table('tenant_users')
                           ->where('tenant_id', $leaveRequest->tenant_id)
                           ->where('employee_id', $employee->id)
                           ->whereNull('deleted_at')
                           ->first();
            if ($userRecord && $userRecord->email) {
                $link = 'http://' . request()->getHost() . '/my/leave';
                Mail::to($userRecord->email)->send(new LeaveRequestDecided($leaveRequest, $link));
            }
        } catch (\Exception $e) {
            \Log::error('Leave rejection email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Leave request rejected.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        // Restore balance if leave was approved
        if ($leaveRequest->status === 'approved') {
            LeaveBalance::restore(
                $leaveRequest->tenant_id,
                $leaveRequest->employee_id,
                $leaveRequest->leave_type_id,
                $leaveRequest->days_requested,
                now()->year
            );
        }

        $name = $leaveRequest->employee->full_name;
        $leaveRequest->delete();
        AuditLog::log('deleted', 'Leave', "Deleted leave request for {$name}");
        return redirect()->route('tenant.leave-requests.index')
                         ->with('success', 'Leave request deleted.');
    }
}




