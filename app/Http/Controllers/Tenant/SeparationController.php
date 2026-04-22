<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Separation;
use App\Models\Tenant\ClearanceItem;
use App\Models\Tenant\ExitInterview;
use App\Models\Tenant\Employee;
use App\Models\Tenant\Loan;
use App\Models\Tenant\ExpenseClaim;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SeparationController extends Controller
{
    public function index()
    {
        $separations = Separation::with('employee')->where('tenant_id', tenant('id'))->latest()->paginate(15);
        $stats = [
            'pending'     => Separation::where('tenant_id', tenant('id'))->where('status', 'pending')->count(),
            'in_progress' => Separation::where('tenant_id', tenant('id'))->where('status', 'in_progress')->count(),
            'completed'   => Separation::where('tenant_id', tenant('id'))->where('status', 'completed')->count(),
        ];
        return view('tenant.separations.index', compact('separations', 'stats'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', tenant('id'))->where('employment_status', 'active')->get();
        return view('tenant.separations.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'      => ['required', 'exists:employees,id'],
            'type'             => ['required'],
            'notice_date'      => ['required', 'date'],
            'last_working_date'=> ['required', 'date'],
            'effective_date'   => ['required', 'date'],
        ]);

        $employee = Employee::find($request->employee_id);

        // Calculate loan balance
        $loanBalance = Loan::where('tenant_id', tenant('id'))
                          ->where('employee_id', $request->employee_id)
                          ->where('status', 'active')
                          ->sum('balance');

        // Calculate pending expense claims
        $pendingClaims = ExpenseClaim::where('tenant_id', tenant('id'))
                            ->where('employee_id', $request->employee_id)
                            ->where('status', 'approved')
                            ->sum('total_amount');

        // Calculate gratuity (basic salary * years of service / 12 * gratuity rate)
        $yearsOfService = 0;
        if ($employee->date_of_joining) {
            $yearsOfService = Carbon::parse($employee->date_of_joining)->diffInYears(now());
        }
        $gratuity = ($employee->basic_salary ?? 0) * $yearsOfService * 0.15;

        $finalDues = $gratuity + $pendingClaims - $loanBalance;

        $separation = Separation::create([
            'tenant_id'         => tenant('id'),
            'employee_id'       => $request->employee_id,
            'type'              => $request->type,
            'notice_date'       => $request->notice_date,
            'last_working_date' => $request->last_working_date,
            'effective_date'    => $request->effective_date,
            'reason'            => $request->reason,
            'loan_balance'      => $loanBalance,
            'pending_claims'    => $pendingClaims,
            'gratuity'          => $gratuity,
            'final_dues'        => $finalDues,
            'status'            => 'pending',
            'notes'             => $request->notes,
        ]);

        // Auto-generate clearance checklist
        $clearanceItems = [
            ['department' => 'IT', 'item' => 'Return laptop and accessories'],
            ['department' => 'IT', 'item' => 'Revoke system access and email'],
            ['department' => 'Finance', 'item' => 'Clear outstanding advances'],
            ['department' => 'Finance', 'item' => 'Process final salary'],
            ['department' => 'HR', 'item' => 'Return ID badge and access cards'],
            ['department' => 'HR', 'item' => 'Complete exit interview'],
            ['department' => 'HR', 'item' => 'Return employee handbook'],
            ['department' => 'Admin', 'item' => 'Return office keys'],
            ['department' => 'Admin', 'item' => 'Clear personal belongings'],
            ['department' => 'Department Head', 'item' => 'Knowledge transfer completed'],
            ['department' => 'Department Head', 'item' => 'Handover document signed'],
        ];

        foreach ($clearanceItems as $item) {
            ClearanceItem::create([
                'tenant_id'     => tenant('id'),
                'separation_id' => $separation->id,
                'department'    => $item['department'],
                'item'          => $item['item'],
                'status'        => 'pending',
            ]);
        }

        // Create exit interview record
        ExitInterview::create([
            'tenant_id'     => tenant('id'),
            'separation_id' => $separation->id,
            'employee_id'   => $request->employee_id,
            'is_submitted'  => false,
        ]);

        return redirect()->route('tenant.separations.show', $separation)->with('success', 'Separation initiated successfully!');
    }

    public function show(Separation $separation)
    {
        if ($separation->tenant_id !== tenant('id')) abort(403);
        $separation->load(['employee', 'clearanceItems', 'exitInterview']);
        return view('tenant.separations.show', compact('separation'));
    }

    public function clearItem(Request $request, ClearanceItem $item)
    {
        if ($item->tenant_id !== tenant('id')) abort(403);
        $item->update([
            'status'     => $request->status ?? 'cleared',
            'cleared_by' => auth()->id(),
            'cleared_at' => now(),
            'notes'      => $request->notes,
        ]);

        // Check if all items cleared
        $separation = $item->separation;
        $allCleared = $separation->clearanceItems()->where('status', 'pending')->count() === 0;
        if ($allCleared) {
            $separation->update(['status' => 'cleared']);
        } else {
            $separation->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Clearance item updated!');
    }

    public function complete(Separation $separation)
    {
        if ($separation->tenant_id !== tenant('id')) abort(403);
        $separation->update([
            'status'      => 'completed',
            'approved_by' => auth()->id(),
        ]);

        // Update employee status
        $separation->employee->update(['employment_status' => 'terminated']);

        return back()->with('success', 'Separation completed! Employee status updated.');
    }

    public function submitExitInterview(Request $request, ExitInterview $interview)
    {
        if ($interview->tenant_id !== tenant('id')) abort(403);
        $interview->update([
            'rating_overall'          => $request->rating_overall,
            'rating_management'       => $request->rating_management,
            'rating_work_environment' => $request->rating_work_environment,
            'rating_compensation'     => $request->rating_compensation,
            'rating_growth'           => $request->rating_growth,
            'reason_leaving'          => $request->reason_leaving,
            'what_worked_well'        => $request->what_worked_well,
            'what_could_improve'      => $request->what_could_improve,
            'would_recommend'         => $request->would_recommend,
            'would_return'            => $request->boolean('would_return'),
            'additional_comments'     => $request->additional_comments,
            'is_submitted'            => true,
            'submitted_at'            => now(),
        ]);
        return back()->with('success', 'Exit interview submitted!');
    }

    public function generateCertificate(Separation $separation)
    {
        if ($separation->tenant_id !== tenant('id')) abort(403);
        $separation->update(['certificate_issued' => true, 'certificate_date' => now()]);
        $employee = $separation->employee;

        $content = "CERTIFICATE OF SERVICE\n\n";
        $content .= "This is to certify that " . $employee->first_name . " " . $employee->last_name . "\n";
        $content .= "Employee Number: " . $employee->employee_number . "\n";
        $content .= "was employed by " . tenant('name') . "\n";
        $content .= "from " . ($employee->date_of_joining ? Carbon::parse($employee->date_of_joining)->format('F d, Y') : 'N/A') . "\n";
        $content .= "to " . $separation->last_working_date->format('F d, Y') . "\n\n";
        $content .= "Position held: " . ($employee->job_title ?? 'N/A') . "\n";
        $content .= "Department: " . ($employee->department ?? 'N/A') . "\n\n";
        $content .= "During this period, the employee served diligently and professionally.\n\n";
        $content .= "Issued on: " . now()->format('F d, Y') . "\n";

        return response($content, 200, [
            'Content-Type'        => 'text/plain',
            'Content-Disposition' => 'attachment; filename=Certificate_of_Service_' . $employee->employee_number . '.txt',
        ]);
    }
}
