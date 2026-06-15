<?php
// app/Mail/LeavePermissionNotification.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeavePermissionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $type; // 'leave' or 'permission'
    public $employee;
    public $manager;
    public $teamLead;
    public $details;

    public function __construct($type, $employee, $manager, $teamLead, $details)
    {
        $this->type = $type;
        $this->employee = $employee;
        $this->manager = $manager;
        $this->teamLead = $teamLead;
        $this->details = $details;
    }

    public function build()
    {
        $subject = $this->type == 'leave' 
            ? 'Leave Request Notification' 
            : 'Permission Request Notification';
        
        $view = $this->type == 'leave' 
            ? 'emails.leave-notification' 
            : 'emails.permission-notification';

        return $this->view($view)
                    ->subject($subject)
                    ->with([
                        'employeeName' => $this->employee->firstname . ' ' . $this->employee->lastname,
                        'employeeId' => $this->employee->employeeid,
                        'details' => $this->details
                    ]);
    }
}