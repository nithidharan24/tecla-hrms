<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HikeLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $body;
    public string $attachmentName;
    public string $attachmentMime;
    public string $attachmentData;

    /**
     * @param string $subject
     * @param string $body
     * @param string $attachmentData raw PDF bytes
     * @param string $attachmentName filename for attachment
     * @param string $attachmentMime default application/pdf
     */
    public function __construct(
        string $subject,
        string $body,
        string $attachmentData,
        string $attachmentName = 'hike-letter.pdf',
        string $attachmentMime = 'application/pdf',
    ) {
        $this->subjectLine = $subject;
        $this->body = $body;
        $this->attachmentData = $attachmentData;
        $this->attachmentName = $attachmentName;
        $this->attachmentMime = $attachmentMime;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hike_letter',
            with: [
                'mailBody' => $this->body,
            ],
        );
    }

    public function attachments(): array
    {
        // Attach the PDF bytes as an attachment
        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(fn () => $this->attachmentData, $this->attachmentName)
                ->withMime($this->attachmentMime),
        ];
    }
}
