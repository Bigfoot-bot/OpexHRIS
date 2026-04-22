<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportTicketReplied extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $ticket, public $link) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Support Ticket Reply — ' . $this->ticket->subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.support-ticket-replied');
    }
}
