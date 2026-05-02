<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnouncementAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $announcement, public $recipient, public $sender, public $link) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Announcement: ' . $this->announcement->title);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.announcement-alert');
    }
}
