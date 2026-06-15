<?php

namespace App\Mail\Client;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMailer extends Mailable
{
    use Queueable, SerializesModels;
    public $firstName;
    public $username;
    public $password;

    /**
     * Create a new message instance.
     * 
     * @param  string  $firstName
     * @param  string  $username
     * @param  string  $password
     * @return void
     */
    public function __construct($firstName, $username, $password)
    {
        $this->firstName = $firstName;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Welcome to Our New Client',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'hrms.admin.client.mail.index',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
