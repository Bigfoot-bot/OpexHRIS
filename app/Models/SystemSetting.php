<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'vat_percentage', 'trial_days', 'grace_period_days',
        'invoice_prefix', 'receipt_prefix',
    ];

    protected $casts = [
        'vat_percentage' => 'decimal:2',
    ];

    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'vat_percentage'    => 16,
            'trial_days'        => 30,
            'grace_period_days' => 7,
            'invoice_prefix'    => 'INV',
            'receipt_prefix'    => 'RCP',
        ]);
    }
}
