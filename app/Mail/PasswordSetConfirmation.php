<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordSetConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $user, public $tenantName, public $loginLink) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Password Set Successfully — ' . $this->tenantName . ' HRIS Portal');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.password-set-confirmation');
    }
}
