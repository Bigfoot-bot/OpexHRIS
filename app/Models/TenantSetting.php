<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    protected $table = 'tenant_settings';

    protected $fillable = [
        'tenant_id',
        'key',
        'value',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('tenant_id', tenant('id'))
                       ->where('key', $key)
                       ->first();

        if (!$setting) return $default;

        $decoded = json_decode($setting->value, true);
        return $decoded !== null ? $decoded : $setting->value;
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['tenant_id' => tenant('id'), 'key' => $key],
            ['value'     => is_array($value) ? json_encode($value) : $value]
        );
    }

    public static function getAll(): array
    {
        return self::where('tenant_id', tenant('id'))
                   ->pluck('value', 'key')
                   ->map(function ($value) {
                       $decoded = json_decode($value, true);
                       return $decoded !== null ? $decoded : $value;
                   })
                   ->toArray();
    }
}