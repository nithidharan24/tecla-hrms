<?php

namespace App\Mail\Estimate;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EstimationMailer extends Mailable
{
    use Queueable, SerializesModels;
    public $clientName;
    public $email;
    public $pdfContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($clientName, $email,$pdfContent)
    {
        $this->clientName = $clientName;
        $this->email = $email;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this
            ->subject('Your Estimate')
            ->view('hrms.hr.sales.estimate.estimateMail.index') // Your email view file
            ->attachData($this->pdfContent, 'estimate.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
