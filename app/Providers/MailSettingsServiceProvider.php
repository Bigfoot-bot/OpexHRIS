<?php

namespace App\Providers;

use App\Models\Central\MailSetting;
use Illuminate\Support\ServiceProvider;

class MailSettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            $settings = MailSetting::getSettings();

            if ($settings->is_configured) {
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
            // Silently fail if DB is not ready yet
        }
    }
}
