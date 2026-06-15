<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee, $memo, $pdfPath;

    public function __construct($employee, $memo, $pdfPath)
    {
        $this->employee = $employee;
        $this->memo = $memo;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject('Memo Notification - ' . $this->memo->name)
                    ->view('emails.memo')
                    ->attach($this->pdfPath, [
                        'as' => 'Memo_' . $this->employee->firstname . '.pdf',
                    ]);
    }
}
