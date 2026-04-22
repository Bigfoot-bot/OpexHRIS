<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $user, public $tenantName, public $link) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to ' . $this->tenantName . ' — HRIS Portal');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.employee-welcome');
    }
}
