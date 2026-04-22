<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\LeaveType;
use App\Models\Tenant\LeaveBalance;
use Illuminate\Support\Facades\DB;

class AllocateLeaveBalances extends Command
{
    protected $signature = 'leave:allocate {tenant_id} {year?}';
    protected $description = 'Allocate leave balances for all employees in a tenant';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $year     = $this->argument('year') ?? now()->year;

        $employees  = DB::table('employees')->where('tenant_id', $tenantId)->where('employment_status', 'active')->get();
        $leaveTypes = DB::table('leave_types')->where('tenant_id', $tenantId)->where('is_active', true)->get();

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                LeaveBalance::allocate($tenantId, $employee->id, $leaveType->id, $leaveType->days_allowed, $year);
                $this->info("Allocated {$leaveType->days_allowed} days of {$leaveType->name} for {$employee->first_name} {$employee->last_name}");
            }
        }

        $this->info('Leave balances allocated successfully!');
    }
}
