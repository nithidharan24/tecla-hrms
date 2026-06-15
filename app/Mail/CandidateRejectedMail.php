<?php

namespace App\Mail;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $candidate;

    /**
     * Create a new message instance.
     */
    public function __construct(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Status Update - ' . $this->candidate->position_applied,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.candidate-rejected',
            with: [
                'candidate' => $this->candidate,
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
        return [];
    }
}