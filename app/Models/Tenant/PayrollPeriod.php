<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'month',
        'year',
        'start_date',
        'end_date',
        'payment_date',
        'status',
        'approved_by',
        'approved_at',
        'payment_mode',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'payment_date'=> 'date',
        'approved_at' => 'datetime',
    ];

    public function records()
    {
        return $this->hasMany(PayrollRecord::class);
    }

    public function getMonthNameAttribute(): string
    {
        return \Carbon\Carbon::create()->month($this->month)->format('F');
    }
}