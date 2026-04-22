<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'status',
        'subscription_plan',
        'amount',
        'tax',
        'total',
        'issue_date',
        'due_date',
        'paid_date',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date'   => 'date',
        'paid_date'  => 'date',
        'amount'     => 'decimal:2',
        'tax'        => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'paid'      => 'emerald',
            'sent'      => 'blue',
            'overdue'   => 'red',
            'cancelled' => 'gray',
            default     => 'amber',
        };
    }

    public static function generateNumber(): string
    {
        $last = self::latest()->first();
        $number = $last ? (int) substr($last->invoice_number, 4) + 1 : 1;
        return 'INV-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function generateForTenant(Tenant $tenant): self
    {
        $planPrices = [
            'basic'        => 10000,
            'professional' => 25000,
            'enterprise'   => 90000,
        ];

        $amount = $planPrices[$tenant->subscription_plan] ?? 0;
        $tax    = round($amount * 0.16, 2); // 16% VAT
        $total  = $amount + $tax;

        return self::create([
            'tenant_id'         => $tenant->id,
            'invoice_number'    => self::generateNumber(),
            'status'            => 'sent',
            'subscription_plan' => $tenant->subscription_plan,
            'amount'            => $amount,
            'tax'               => $tax,
            'total'             => $total,
            'issue_date'        => now(),
            'due_date'          => now()->addDays(30),
        ]);
    }
}