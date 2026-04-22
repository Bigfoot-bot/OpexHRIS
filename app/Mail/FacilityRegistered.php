<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacilityRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $tenant) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Facility Registered — ' . $this->tenant->name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.facility-registered');
    }
}
