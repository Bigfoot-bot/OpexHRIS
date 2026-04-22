<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    protected $fillable = [
        'invoice_number', 'tenant_id', 'plan_id', 'cycle',
        'subtotal', 'discount_amount', 'vat_percentage', 'vat_amount',
        'total', 'due_date', 'status', 'receipt_number', 'paid_at',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_percentage'  => 'decimal:2',
        'vat_amount'      => 'decimal:2',
        'total'           => 'decimal:2',
        'due_date'        => 'date',
        'paid_at'         => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public static function generateNumber(): string
    {
        $settings = SystemSetting::getSettings();
        $prefix   = $settings->invoice_prefix ?? 'INV';
        $count    = static::count() + 1;
        return $prefix . '-' . now()->format('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function generateReceiptNumber(): string
    {
        $settings = SystemSetting::getSettings();
        $prefix   = $settings->receipt_prefix ?? 'RCP';
        $count    = static::whereNotNull('receipt_number')->count() + 1;
        return $prefix . '-' . now()->format('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
