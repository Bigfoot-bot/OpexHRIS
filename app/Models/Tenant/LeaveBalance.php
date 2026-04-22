<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $table = 'leave_balances';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'leave_type_id',
        'year',
        'allocated_days',
        'used_days',
        'remaining_days',
    ];

    protected $casts = [
        'allocated_days'  => 'decimal:1',
        'used_days'       => 'decimal:1',
        'remaining_days'  => 'decimal:1',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public static function allocate($tenantId, $employeeId, $leaveTypeId, $days, $year = null)
    {
        $year = $year ?? now()->year;

        return static::firstOrCreate(
            [
                'tenant_id'     => $tenantId,
                'employee_id'   => $employeeId,
                'leave_type_id' => $leaveTypeId,
                'year'          => $year,
            ],
            [
                'allocated_days'  => $days,
                'used_days'       => 0,
                'remaining_days'  => $days,
            ]
        );
    }

    public static function deduct($tenantId, $employeeId, $leaveTypeId, $days, $year = null)
    {
        $year = $year ?? now()->year;

        $balance = static::where('tenant_id', $tenantId)
                         ->where('employee_id', $employeeId)
                         ->where('leave_type_id', $leaveTypeId)
                         ->where('year', $year)
                         ->first();

        if ($balance) {
            $balance->update([
                'used_days'      => $balance->used_days + $days,
                'remaining_days' => max(0, $balance->remaining_days - $days),
            ]);
        }

        return $balance;
    }

    public static function restore($tenantId, $employeeId, $leaveTypeId, $days, $year = null)
    {
        $year = $year ?? now()->year;

        $balance = static::where('tenant_id', $tenantId)
                         ->where('employee_id', $employeeId)
                         ->where('leave_type_id', $leaveTypeId)
                         ->where('year', $year)
                         ->first();

        if ($balance) {
            $balance->update([
                'used_days'      => max(0, $balance->used_days - $days),
                'remaining_days' => min($balance->allocated_days, $balance->remaining_days + $days),
            ]);
        }

        return $balance;
    }
}
