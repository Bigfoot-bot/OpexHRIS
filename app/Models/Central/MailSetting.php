<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'is_configured',
    ];

    protected $casts = [
        'is_configured' => 'boolean',
        'mail_port'     => 'integer',
    ];

    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'mail_host'         => 'smtp.gmail.com',
            'mail_port'         => 587,
            'mail_encryption'   => 'tls',
            'mail_from_name'    => 'OpEx HRIS',
            'is_configured'     => false,
        ]);
    }
}
