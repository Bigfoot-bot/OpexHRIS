<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\LeaveBalance;
use Illuminate\Support\Facades\DB;

class ResetLeaveBalances extends Command
{
    protected $signature = 'leave:reset-balances';
    protected $description = 'Reset leave balances for all employees at start of new year';

    public function handle()
    {
        $year = now()->year;

        // Get all tenants
        $tenants = DB::table('tenants')->get();

        foreach ($tenants as $tenant) {
            $employees  = DB::table('employees')
                            ->where('tenant_id', $tenant->id)
                            ->where('employment_status', 'active')
                            ->get();

            $leaveTypes = DB::table('leave_types')
                            ->where('tenant_id', $tenant->id)
                            ->where('is_active', true)
                            ->get();

            foreach ($employees as $employee) {
                foreach ($leaveTypes as $leaveType) {
                    // Check if balance already exists for this year
                    $exists = DB::table('leave_balances')
                                ->where('tenant_id', $tenant->id)
                                ->where('employee_id', $employee->id)
                                ->where('leave_type_id', $leaveType->id)
                                ->where('year', $year)
                                ->exists();

                    if (!$exists) {
                        DB::table('leave_balances')->insert([
                            'tenant_id'      => $tenant->id,
                            'employee_id'    => $employee->id,
                            'leave_type_id'  => $leaveType->id,
                            'year'           => $year,
                            'allocated_days' => $leaveType->days_allowed,
                            'used_days'      => 0,
                            'remaining_days' => $leaveType->days_allowed,
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);

                        $this->info("Allocated {$leaveType->days_allowed} days of {$leaveType->name} for employee ID {$employee->id}");
                    }
                }
            }
        }

        $this->info('Leave balances reset successfully for ' . $year . '!');
    }
}
