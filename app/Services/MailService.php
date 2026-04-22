<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\SymfonyMailer;

class MailService
{
    public static function getConfiguredMailer()
    {
        $settings = \DB::connection('mysql')->table('mail_settings')->first();

        if (!$settings || !$settings->is_configured) {
            return Mail::getFacadeRoot();
        }

        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            $settings->mail_host,
            $settings->mail_port,
            $settings->mail_encryption === 'tls'
        );

        $transport->setUsername($settings->mail_username);
        $transport->setPassword($settings->mail_password);

        $symfonyMailer = new \Symfony\Component\Mailer\Mailer($transport);
        $mailer = new \Illuminate\Mail\Mailer(
            'smtp',
            app('view'),
            new \Illuminate\Mail\SymfonyMailer($symfonyMailer),
            app('events')
        );

        $mailer->alwaysFrom($settings->mail_from_address, $settings->mail_from_name);

        return $mailer;
    }

    public static function send($to, $mailable)
    {
        static::getConfiguredMailer()->to($to)->send($mailable);
    }
}
