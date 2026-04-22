<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilitySubscription extends Model
{
    protected $fillable = [
        'tenant_id', 'plan_id', 'cycle', 'amount_paid', 'vat_amount',
        'discount_amount', 'start_date', 'end_date', 'status',
        'auto_renew', 'suspended_at', 'reminder_sent_at',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'suspended_at'      => 'datetime',
        'reminder_sent_at'  => 'datetime',
        'auto_renew'        => 'boolean',
        'amount_paid'       => 'decimal:2',
        'vat_amount'        => 'decimal:2',
        'discount_amount'   => 'decimal:2',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    public function daysRemaining(): int
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public static function getOrCreate(string $tenantId): self
    {
        return static::where('tenant_id', $tenantId)->latest()->first()
            ?? static::create([
                'tenant_id'  => $tenantId,
                'plan_id'    => SubscriptionPlan::first()->id,
                'cycle'      => 'monthly',
                'start_date' => now(),
                'end_date'   => now()->addDays(30),
                'status'     => 'trial',
            ]);
    }
}
