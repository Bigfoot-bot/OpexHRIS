<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuperAdminPasswordChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $admin) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Super Admin Password Has Been Changed — OpEx HRIS');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.super-admin-password-changed');
    }
}
