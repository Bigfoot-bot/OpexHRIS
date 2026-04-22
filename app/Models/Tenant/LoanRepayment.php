<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'loan_id', 'employee_id', 'amount',
        'due_date', 'paid_date', 'status', 'payment_method', 'notes',
    ];
    protected $casts = [
        'due_date'  => 'date',
        'paid_date' => 'date',
        'amount'    => 'decimal:2',
    ];
    public function loan() { return $this->belongsTo(Loan::class); }
}
