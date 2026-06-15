<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GoalAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $goal;
    public $employeeName;
    public $assignedByName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($goal, $employeeName, $assignedByName)
    {
        $this->goal = $goal;
        $this->employeeName = $employeeName;
        $this->assignedByName = $assignedByName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Goal Assigned: ' . $this->goal->goal_title)
                    ->view('emails.goal-assigned');
    }
}