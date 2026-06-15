<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function build()
    {
        $subject = $this->getSubject();
        $this->mailData['subject'] = $subject;
        
        return $this->subject($subject)
                    ->view('emails.schedule-notification')
                    ->with('data', $this->mailData);
    }

    private function getSubject()
    {
        switch ($this->mailData['type']) {
            case 'created':
                return 'New Schedule Assignment - ' . $this->mailData['employee_name'];
            case 'updated':
                return 'Schedule Updated - ' . $this->mailData['employee_name'];
            case 'new_version':
                return 'New Upcoming Schedule - ' . $this->mailData['employee_name'];
            case 'cancelled':
                return 'Schedule Cancelled - ' . $this->mailData['employee_name'];
            case 'interchange_request':
                return 'Shift Interchange Request - Action Required';
            case 'interchange_approved':
                return 'Shift Interchange Approved - ' . ($this->mailData['is_requester'] ? 'Your Request' : 'Request for You');
            case 'interchange_rejected':
                return 'Shift Interchange Rejected - ' . ($this->mailData['is_requester'] ? 'Your Request' : 'Request for You');
            default:
                return 'Schedule Notification';
        }
    }
}