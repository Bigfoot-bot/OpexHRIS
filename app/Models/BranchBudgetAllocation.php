<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchBudgetAllocation extends Model
{
    protected $fillable = [
        'tenant_id', 'branch_id', 'allocated_amount', 'used_amount', 'period', 'notes',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'used_amount'      => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getRemainingAttribute()
    {
        return $this->allocated_amount - $this->used_amount;
    }
}
