<?php
namespace App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    protected $fillable = [
        'expense_claim_id', 'category', 'description',
        'date', 'amount', 'receipt_path', 'receipt_name',
    ];
    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];
    public function claim()
    {
        return $this->belongsTo(ExpenseClaim::class, 'expense_claim_id');
    }
}
