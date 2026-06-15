<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AMCRenewalReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $daysLeft;
    public $renewalDate;

    /**
     * Create a new message instance.
     */
    public function __construct($client, $daysLeft, $renewalDate)
    {
        $this->client = $client;
        $this->daysLeft = $daysLeft;
        $this->renewalDate = $renewalDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'AMC Renewal Reminder - ' . $this->daysLeft . ' Days Left',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.amc_renewal_reminder',
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