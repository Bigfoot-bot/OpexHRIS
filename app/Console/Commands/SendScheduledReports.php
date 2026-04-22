<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\ScheduledReport;
use App\Http\Controllers\Tenant\ReportBuilderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Stancl\Tenancy\Facades\Tenancy;

class SendScheduledReports extends Command
{
    protected $signature   = 'reports:send-scheduled';
    protected $description = 'Send scheduled reports to recipients';

    public function handle(): void
    {
        $this->info('Sending scheduled reports...');

        // Get all tenants
        $tenants = \App\Models\Central\Tenant::all();

        foreach ($tenants as $tenant) {
            Tenancy::initialize($tenant);

            $reports = ScheduledReport::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->where('next_send_at', '<=', Carbon::now())
                ->get();

            foreach ($reports as $report) {
                try {
                    $this->sendReport($report, $tenant);
                    $this->info("Sent: {$report->name} for {$tenant->name}");
                } catch (\Exception $e) {
                    $this->error("Failed: {$report->name} - " . $e->getMessage());
                }
            }

            Tenancy::end();
        }

        $this->info('Done!');
    }

    private function sendReport(ScheduledReport $report, $tenant): void
    {
        // Generate report data
        $controller = new ReportBuilderController();
        $request    = Request::create('/report-builder', 'POST', ['report_type' => $report->report_type, 'format' => 'csv']);
        if ($report->filters) {
            foreach ($report->filters as $key => $value) {
                $request->merge([$key => $value]);
            }
        }

        // Build CSV content
        $data    = $this->getReportData($report->report_type, $tenant->id);
        $csv     = $this->buildCsv($data);
        $filename = $report->report_type . '_' . date('Y-m-d') . '.csv';

        // Send to each recipient
        $recipients = $report->recipients_array;
        foreach ($recipients as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
            Mail::send('emails.scheduled-report', [
                'report'     => $report,
                'tenantName' => $tenant->name,
                'date'       => now()->format('M d, Y'),
            ], function ($m) use ($email, $report, $csv, $filename, $tenant) {
                $m->to($email)
                  ->subject("[{$tenant->name}] Scheduled Report: {$report->name}")
                  ->attachData($csv, $filename, ['mime' => 'text/csv']);
            });
        }

        // Update next send time
        $frequency = $report->frequency;
        $nextSend  = match ($frequency) {
            'daily'   => Carbon::now()->addDay()->setTimeFromTimeString($report->send_time),
            'weekly'  => Carbon::now()->addWeek()->setTimeFromTimeString($report->send_time),
            'monthly' => Carbon::now()->addMonth()->setTimeFromTimeString($report->send_time),
            default   => Carbon::now()->addDay(),
        };

        $report->update(['last_sent_at' => now(), 'next_send_at' => $nextSend]);
    }

    private function getReportData(string $type, string $tenantId): array
    {
        switch ($type) {
            case 'employees':
                return \App\Models\Tenant\Employee::where('tenant_id', $tenantId)->get()->map(fn($e) => [
                    $e->employee_number, $e->first_name . ' ' . $e->last_name,
                    $e->department, $e->job_title, $e->employment_status,
                ])->toArray();
            case 'leave':
                return \App\Models\Tenant\LeaveRequest::where('tenant_id', $tenantId)->with('employee')->get()->map(fn($l) => [
                    $l->employee->first_name . ' ' . $l->employee->last_name,
                    $l->start_date, $l->end_date, $l->days, $l->status,
                ])->toArray();
            default:
                return [];
        }
    }

    private function buildCsv(array $data): string
    {
        $output = fopen('php://temp', 'w');
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
    }
}
