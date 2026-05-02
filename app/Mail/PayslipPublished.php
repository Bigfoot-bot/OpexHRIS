<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayslipPublished extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $user,
        public $period,
        public $netPay,
        public $link,
        public string $pdfContent = '',
        public string $pdfFilename = 'payslip.pdf'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Payslip for ' . $this->period . ' is Ready');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notifications.payslip-published');
    }

    public function attachments(): array
    {
        if ($this->pdfContent === '') {
            return [];
        }

        return [
            Attachment::fromData(fn() => $this->pdfContent, $this->pdfFilename)
                ->withMime('application/pdf'),
        ];
    }
}
