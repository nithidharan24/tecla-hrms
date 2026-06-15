<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CandidateEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $emailSubject;
    public $emailMessage;
    public $emailType;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($application, $subject, $message, $type)
    {
        $this->application = $application;
        $this->emailSubject = $subject;
        $this->emailMessage = $message;
        $this->emailType = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Replace placeholders in subject and message
        $subject = str_replace(
            ['{candidate_name}', '{job_title}', '{department}'],
            [$this->application->first_name . ' ' . $this->application->last_name, $this->application->job_title, $this->application->department],
            $this->emailSubject
        );

        $message = str_replace(
            ['{candidate_name}', '{job_title}', '{department}'],
            [$this->application->first_name . ' ' . $this->application->last_name, $this->application->job_title, $this->application->department],
            $this->emailMessage
        );

        return $this->subject($subject)
                    ->view('emails.candidate-email')
                    ->with([
                        'candidateName' => $this->application->first_name . ' ' . $this->application->last_name,
                        'jobTitle' => $this->application->job_title,
                        'department' => $this->application->department,
                        'emailMessage' => $message,
                        'emailType' => $this->emailType
                    ]);
    }
}
