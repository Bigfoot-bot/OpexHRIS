<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\ScheduledReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduledReportController extends Controller
{
    public function index()
    {
        $reports = ScheduledReport::where('tenant_id', tenant('id'))->latest()->paginate(15);
        $reportTypes = [
            'employees' => 'Employee Report',
            'leave'     => 'Leave Report',
            'payroll'   => 'Payroll Report',
            'overtime'  => 'Overtime Report',
            'loans'     => 'Loans Report',
            'expenses'  => 'Expense Claims Report',
            'headcount' => 'Headcount Report',
        ];
        return view('tenant.scheduled-reports.index', compact('reports', 'reportTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string'],
            'report_type' => ['required', 'string'],
            'frequency'   => ['required', 'in:daily,weekly,monthly'],
            'send_time'   => ['required'],
            'recipients'  => ['required', 'string'],
        ]);

        $nextSend = $this->calculateNextSend($request->frequency, $request->send_time, $request->day_of_week, $request->day_of_month);

        ScheduledReport::create([
            'tenant_id'    => tenant('id'),
            'name'         => $request->name,
            'report_type'  => $request->report_type,
            'frequency'    => $request->frequency,
            'day_of_week'  => $request->day_of_week,
            'day_of_month' => $request->day_of_month,
            'send_time'    => $request->send_time,
            'format'       => $request->format ?? 'csv',
            'recipients'   => $request->recipients,
            'is_active'    => true,
            'next_send_at' => $nextSend,
            'created_by'   => auth()->id(),
        ]);

        return back()->with('success', 'Scheduled report created successfully!');
    }

    public function toggle(ScheduledReport $report)
    {
        if ($report->tenant_id !== tenant('id')) abort(403);
        $report->update(['is_active' => !$report->is_active]);
        return back()->with('success', 'Report ' . ($report->is_active ? 'activated' : 'deactivated') . '!');
    }

    public function destroy(ScheduledReport $report)
    {
        if ($report->tenant_id !== tenant('id')) abort(403);
        $report->delete();
        return back()->with('success', 'Scheduled report deleted!');
    }

    private function calculateNextSend(string $frequency, string $time, $dayOfWeek = null, $dayOfMonth = null): Carbon
    {
        $now = Carbon::now();
        switch ($frequency) {
            case 'daily':
                $next = Carbon::today()->setTimeFromTimeString($time);
                if ($next <= $now) $next->addDay();
                return $next;
            case 'weekly':
                $days = ['mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 0];
                $targetDay = $days[$dayOfWeek ?? 'mon'] ?? 1;
                $next = Carbon::now()->next($targetDay)->setTimeFromTimeString($time);
                return $next;
            case 'monthly':
                $day = $dayOfMonth ?? 1;
                $next = Carbon::now()->startOfMonth()->addDays($day - 1)->setTimeFromTimeString($time);
                if ($next <= $now) $next->addMonth();
                return $next;
            default:
                return Carbon::tomorrow()->setTimeFromTimeString($time);
        }
    }
}
