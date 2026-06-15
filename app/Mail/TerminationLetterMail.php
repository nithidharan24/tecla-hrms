<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment; // Ensure this is imported

class TerminationLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $termination;
    public $pdfContent; // New property for dynamic PDF content
    public $emailSubject; // New property for dynamic email subject
    public $htmlEmailContent; // New property for dynamic HTML email body

    /**
     * Create a new message instance.
     */
    public function __construct($employee, $termination, $pdfContent, $emailSubject, $htmlEmailContent)
    {
        $this->employee = $employee;
        $this->termination = $termination;
        $this->pdfContent = $pdfContent;
        $this->emailSubject = $emailSubject;
        $this->htmlEmailContent = $htmlEmailContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // This method is typically used for defining a Blade view.
        // Since we are passing raw HTML, we will handle it in the build method.
        // If you were using a Blade view file, it would look like:
        // return new Content(
        //     view: 'emails.termination-letter',
        //     with: [
        //         'employee' => $this->employee,
        //         'termination' => $this->termination,
        //     ],
        // );
        return new Content(); // Return an empty content definition as HTML is set in build()
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'termination_letter.pdf')
                ->withMime('application/pdf'),
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailSubject)
                    ->html($this->htmlEmailContent); // Use html() for raw HTML content
    }
}
