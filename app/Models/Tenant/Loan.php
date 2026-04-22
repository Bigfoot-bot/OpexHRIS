<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'employee_id', 'loan_number', 'type', 'amount',
        'interest_rate', 'repayment_months', 'monthly_deduction',
        'total_repayable', 'balance', 'disbursement_date',
        'start_repayment_date', 'status', 'purpose', 'approved_by', 'approved_at',
    ];
    protected $casts = [
        'disbursement_date'    => 'date',
        'start_repayment_date' => 'date',
        'approved_at'          => 'datetime',
        'amount'               => 'decimal:2',
        'balance'              => 'decimal:2',
        'monthly_deduction'    => 'decimal:2',
        'total_repayable'      => 'decimal:2',
    ];
    public function employee() { return $this->belongsTo(Employee::class); }
    public function repayments() { return $this->hasMany(LoanRepayment::class); }
}
