<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HikeLetterPdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectText;
    public string $bodyText;
    protected string $pdfContent;
    protected string $pdfFilename;

    public function __construct(string $subjectText, string $bodyText, string $pdfContent, string $pdfFilename)
    {
        $this->subjectText = $subjectText;
        $this->bodyText = $bodyText;
        $this->pdfContent = $pdfContent;
        $this->pdfFilename = $pdfFilename;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hike_notification',
            with: [
                'subject'  => $this->subjectText,  // added
                'mailBody' => $this->bodyText,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->pdfFilename)
                ->withMime('application/pdf'),
        ];
    }
}
