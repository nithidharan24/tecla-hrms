<?php

namespace App\Mail;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CandidateSelectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $candidate;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Candidate $candidate, $pdfContent = null)
    {
        $this->candidate = $candidate;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! You have been selected - ' . $this->candidate->position_applied,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.candidate-selected',
            with: [
                'candidate' => $this->candidate,
                'hasOfferLetter' => !is_null($this->pdfContent),
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
        
        if ($this->pdfContent && !empty($this->pdfContent)) {
            try {
                $attachments[] = Attachment::fromData(
                    fn() => $this->pdfContent,
                    'Offer-Letter-' . $this->candidate->first_name . '-' . $this->candidate->last_name . '.pdf'
                )->withMime('application/pdf');
            } catch (\Exception $e) {
                // Log attachment error but don't fail the email
                \Log::error('Failed to attach offer letter PDF', [
                    'candidate_id' => $this->candidate->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $attachments;
    }
}
