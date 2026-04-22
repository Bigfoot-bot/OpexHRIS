<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BrandingSetting extends Model
{
    protected $fillable = [
        'platform_name',
        'platform_tagline',
        'logo',
        'favicon',
        'primary_color',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_branch',
        'paybill_number',
        'mpesa_account',
    ];
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'platform_name'    => 'OpEx HRIS',
            'platform_tagline' => 'Healthcare HR Management Platform',
            'primary_color'    => '#064e3b',
        ]);
    }
}

