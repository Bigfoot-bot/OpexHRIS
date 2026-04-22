<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PayrollRecord extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'payroll_period_id',
        'employee_id',
        'basic_salary',
        'house_allowance',
        'transport_allowance',
        'medical_allowance',
        'other_allowances',
        'overtime_pay',
        'gross_salary',
        'paye',
        'nhif',
        'nssf_employee',
        'nssf_employer',
        'housing_levy',
        'loan_deduction',
        'other_deductions',
        'total_deductions',
        'net_salary',
    ];

    protected $casts = [
        'basic_salary'        => 'decimal:2',
        'house_allowance'     => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'medical_allowance'   => 'decimal:2',
        'other_allowances'    => 'decimal:2',
        'overtime_pay'        => 'decimal:2',
        'gross_salary'        => 'decimal:2',
        'paye'                => 'decimal:2',
        'nhif'                => 'decimal:2',
        'nssf_employee'       => 'decimal:2',
        'nssf_employer'       => 'decimal:2',
        'housing_levy'        => 'decimal:2',
        'loan_deduction'      => 'decimal:2',
        'other_deductions'    => 'decimal:2',
        'total_deductions'    => 'decimal:2',
        'net_salary'          => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }
}