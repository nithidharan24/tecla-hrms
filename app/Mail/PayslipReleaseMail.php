<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayslipReleaseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $bodyText;
    public $pdfContent;
    public $pdfName;

    public function __construct($subjectLine, $bodyText, $pdfContent, $pdfName)
    {
        $this->subjectLine = $subjectLine;
        $this->bodyText    = $bodyText;
        $this->pdfContent  = $pdfContent;
        $this->pdfName     = $pdfName;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.payslip-release')
            ->with(['bodyText' => $this->bodyText])
            ->attachData($this->pdfContent, $this->pdfName, [
                'mime' => 'application/pdf',
            ]);
    }
}