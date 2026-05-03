<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BranchManagerAppointed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $manager,
        public $branch,
        public string $tenantName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'You have been appointed Branch Manager — ' . $this->branch->name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.branch-manager-appointed');
    }
}
