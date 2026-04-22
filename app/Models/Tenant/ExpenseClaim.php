<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ExpenseClaim extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'employee_id', 'claim_number', 'title',
        'total_amount', 'status', 'claim_date', 'description',
        'approved_by', 'approved_at', 'paid_at', 'rejection_reason',
    ];
    protected $casts = [
        'claim_date'  => 'date',
        'approved_at' => 'datetime',
        'paid_at'     => 'datetime',
        'total_amount'=> 'decimal:2',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }
}
