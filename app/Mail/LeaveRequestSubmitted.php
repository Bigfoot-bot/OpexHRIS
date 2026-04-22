<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $leaveRequest, public $link) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Leave Request — ' . ($this->leaveRequest->employee->full_name ?? 'Employee'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.leave-request-submitted');
    }
}
