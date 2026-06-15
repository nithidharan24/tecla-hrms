<?php

namespace App\Mail\Invoice;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $clientName;
    public $invoiceId;
    public $invoiceDate;
    public $amount;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($clientName, $toEmail, $pdfContent, $invoiceId, $invoiceDate, $amount)
    {
        $this->clientName = $clientName;
        $this->toEmail = $toEmail;
        $this->pdfContent = $pdfContent;
        $this->invoiceId = $invoiceId;
        $this->invoiceDate = $invoiceDate;
        $this->amount = $amount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->toEmail)
            ->subject('Invoice #' . $this->invoiceId . ' - ' . config('app.name'))
            ->view('emails.invoice')
            ->attachData($this->pdfContent, 'Invoice_' . $this->invoiceId . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}