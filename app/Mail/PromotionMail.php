<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment; // Import Attachment

class PromotionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $promotion;
    public $newDesignationName;
    public $oldDesignationName;
    public $promotionLetterPdfContent; // New property for PDF content

    /**
     * Create a new message instance.
     */
    public function __construct($employee, $promotion, $newDesignationName, $oldDesignationName, $promotionLetterPdfContent = null)
    {
        $this->employee = $employee;
        $this->promotion = $promotion;
        $this->newDesignationName = $newDesignationName;
        $this->oldDesignationName = $oldDesignationName;
        $this->promotionLetterPdfContent = $promotionLetterPdfContent; // Assign new property
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations on Your Promotion!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.promotion',
            with: [
                'employee' => $this->employee,
                'promotion' => $this->promotion,
                'newDesignationName' => $this->newDesignationName,
                'oldDesignationName' => $this->oldDesignationName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->promotionLetterPdfContent) {
            $attachments[] = Attachment::fromData(fn () => $this->promotionLetterPdfContent, 'Promotion_Letter_' . $this->employee->employeeid . '.pdf')
                                ->withMime('application/pdf');
        }

        return $attachments;
    }
}