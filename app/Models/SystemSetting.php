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
        // PAYE
        'paye_band1_limit', 'paye_band1_rate',
        'paye_band2_limit', 'paye_band2_rate',
        'paye_band3_limit', 'paye_band3_rate',
        'paye_band4_limit', 'paye_band4_rate',
        'paye_band5_rate',  'paye_personal_relief',
        // SHA
        'sha_rate',
        // NSSF
        'nssf_employee_rate', 'nssf_employer_rate',
        'nssf_tier1_limit',   'nssf_tier2_limit',
        // Housing Levy
        'housing_levy_employee_rate', 'housing_levy_employer_rate',
    ];

    protected $casts = [
        'vat_percentage'             => 'decimal:2',
        'maintenance_mode'           => 'boolean',
        'allow_new_registrations'    => 'boolean',
        'max_login_attempts'         => 'integer',
        'session_lifetime'           => 'integer',
        'paye_band1_limit'           => 'decimal:2',
        'paye_band1_rate'            => 'decimal:2',
        'paye_band2_limit'           => 'decimal:2',
        'paye_band2_rate'            => 'decimal:2',
        'paye_band3_limit'           => 'decimal:2',
        'paye_band3_rate'            => 'decimal:2',
        'paye_band4_limit'           => 'decimal:2',
        'paye_band4_rate'            => 'decimal:2',
        'paye_band5_rate'            => 'decimal:2',
        'paye_personal_relief'       => 'decimal:2',
        'sha_rate'                   => 'decimal:2',
        'nssf_employee_rate'         => 'decimal:2',
        'nssf_employer_rate'         => 'decimal:2',
        'nssf_tier1_limit'           => 'decimal:2',
        'nssf_tier2_limit'           => 'decimal:2',
        'housing_levy_employee_rate' => 'decimal:2',
        'housing_levy_employer_rate' => 'decimal:2',
    ];

    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'vat_percentage'             => 16,
            'trial_days'                 => 30,
            'grace_period_days'          => 7,
            'invoice_prefix'             => 'INV',
            'receipt_prefix'             => 'RCP',
            'maintenance_mode'           => false,
            'maintenance_message'        => 'We are currently performing scheduled maintenance. Please check back shortly.',
            'allow_new_registrations'    => true,
            'support_email'              => null,
            'support_phone'              => null,
            'default_timezone'           => 'Africa/Nairobi',
            'max_login_attempts'         => 5,
            'session_lifetime'           => 120,
            'paye_band1_limit'           => 24000,
            'paye_band1_rate'            => 10,
            'paye_band2_limit'           => 32333,
            'paye_band2_rate'            => 25,
            'paye_band3_limit'           => 500000,
            'paye_band3_rate'            => 30,
            'paye_band4_limit'           => 800000,
            'paye_band4_rate'            => 32.5,
            'paye_band5_rate'            => 35,
            'paye_personal_relief'       => 2400,
            'sha_rate'                   => 2.75,
            'nssf_employee_rate'         => 6,
            'nssf_employer_rate'         => 6,
            'nssf_tier1_limit'           => 7000,
            'nssf_tier2_limit'           => 36000,
            'housing_levy_employee_rate' => 1.5,
            'housing_levy_employer_rate' => 1.5,
        ]);
    }
}
