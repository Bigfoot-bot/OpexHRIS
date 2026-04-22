<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'days_allowed',
        'is_paid',
        'requires_document',
        'allow_half_day',
        'carry_forward',
        'max_carry_forward_days',
        'is_active',
    ];

    protected $casts = [
        'is_paid'           => 'boolean',
        'requires_document' => 'boolean',
        'allow_half_day'    => 'boolean',
        'carry_forward'     => 'boolean',
        'is_active'         => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}