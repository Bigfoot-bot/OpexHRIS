<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class DarajaSetting extends Model
{
    protected $fillable = [
        'consumer_key',
        'consumer_secret',
        'paybill_number',
        'passkey',
        'callback_url',
        'environment',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'environment' => 'sandbox',
            'is_active'   => false,
        ]);
    }

    public function isProduction(): bool
    {
        return $this->environment === 'production';
    }

    public function getBaseUrl(): string
    {
        return $this->isProduction()
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }
}
