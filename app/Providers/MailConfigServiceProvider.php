<?php

namespace App\Providers;

use App\Models\Central\MailSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class MailConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            $settings = DB::connection('mysql')->table('mail_settings')->first();

            if ($settings && $settings->is_configured) {
                config([
                    'mail.default'                 => 'smtp',
                    'mail.mailers.smtp.host'       => $settings->mail_host,
                    'mail.mailers.smtp.port'       => $settings->mail_port,
                    'mail.mailers.smtp.username'   => $settings->mail_username,
                    'mail.mailers.smtp.password'   => $settings->mail_password,
                    'mail.mailers.smtp.encryption' => $settings->mail_encryption,
                    'mail.from.address'            => $settings->mail_from_address,
                    'mail.from.name'               => $settings->mail_from_name,
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail if DB is not ready
        }
    }
}
