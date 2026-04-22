<?php
namespace App\Models\Central;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'label', 'type', 'is_encrypted'];
    protected $casts = ['is_encrypted' => 'boolean'];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        return $setting->is_encrypted ? decrypt($setting->value) : $setting->value;
    }

    public static function set(string $key, $value): void
    {
        $setting = static::where('key', $key)->first();
        if ($setting && $setting->is_encrypted) {
            $value = encrypt($value);
        }
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
