<?php

namespace App\Models\Central;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant
{
    use HasDomains;

    protected $fillable = [
        'mfa_forced',
        'id',
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'county',
        'facility_type',
        'keph_level',
        'bed_capacity',
        'subscription_plan',
        'is_active',
        'logo',
        'primary_color',
        'settings',
        'trial_ends_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'settings'             => 'array',
        'trial_ends_at'        => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'email',
            'phone',
            'address',
            'county',
            'facility_type',
            'keph_level',
            'bed_capacity',
            'subscription_plan',
            'is_active',
            'logo',
            'primary_color',
            'settings',
            'trial_ends_at',
            'subscription_ends_at',
        ];
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'tenant_id');
    }

    public function getMonthlyFeeAttribute(): int
    {
        return match($this->subscription_plan) {
            'basic'        => 10000,
            'professional' => 25000,
            'enterprise'   => 90000,
            default        => 0,
        };
    }

    public function getIsOnTrialAttribute(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
}
