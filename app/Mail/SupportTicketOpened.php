<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportTicketOpened extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $ticket) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Support Ticket — ' . $this->ticket->subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.support-ticket-opened');
    }
}
