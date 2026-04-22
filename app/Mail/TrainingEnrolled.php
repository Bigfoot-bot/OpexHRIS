<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingEnrolled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $user, public $training, public $link) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Training Enrollment — ' . $this->training->title);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.training-enrolled');
    }
}
