<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'vat_percentage', 'trial_days', 'grace_period_days',
        'invoice_prefix', 'receipt_prefix',
        'maintenance_mode', 'maintenance_message', 'allow_new_registrations',
        'support_email', 'support_phone', 'default_timezone',
        'max_login_attempts', 'session_lifetime',
    ];

    protected $casts = [
        'vat_percentage'          => 'decimal:2',
        'maintenance_mode'        => 'boolean',
        'allow_new_registrations' => 'boolean',
        'max_login_attempts'      => 'integer',
        'session_lifetime'        => 'integer',
    ];

    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'vat_percentage'          => 16,
            'trial_days'              => 30,
            'grace_period_days'       => 7,
            'invoice_prefix'          => 'INV',
            'receipt_prefix'          => 'RCP',
            'maintenance_mode'        => false,
            'maintenance_message'     => 'We are currently performing scheduled maintenance. Please check back shortly.',
            'allow_new_registrations' => true,
            'support_email'           => null,
            'support_phone'           => null,
            'default_timezone'        => 'Africa/Nairobi',
            'max_login_attempts'      => 5,
            'session_lifetime'        => 120,
        ]);
    }
}
