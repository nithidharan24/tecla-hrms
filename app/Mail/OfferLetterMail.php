<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class OfferLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $appointment;
    public $pdfBinary;
    public $settings;

    /**
     * Create a new message instance.
     */
    public function __construct($employee, $appointment, $pdfBinary, $settings)
    {
        $this->employee = $employee;
        $this->appointment = $appointment;
        $this->pdfBinary = $pdfBinary;
        $this->settings = $settings;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $companyName = $this->settings['companyName'] ?? 'TECLA MEDIA';
        $designation = $this->appointment->designation ?? ($this->employee->position_applied ?? 'Position');
        $name = ($this->employee->firstname ?? ($this->employee->first_name ?? 'Candidate')) . ' ' . ($this->employee->lastname ?? ($this->employee->last_name ?? ''));
        return new Envelope(
            subject: "Offer Letter for " . trim($name) . " - " . $designation . " at " . $companyName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $candidate = (object)[
            'first_name' => $this->employee->firstname ?? ($this->employee->first_name ?? 'Candidate'),
            'last_name' => $this->employee->lastname ?? ($this->employee->last_name ?? ''),
            'position_applied' => $this->employee->position_applied ?? ($this->appointment->designation ?? 'Position'),
        ];

        $offerData = (object)[
            'designation' => $this->appointment->designation ?? ($this->employee->position_applied ?? 'Position'),
            'department' => $this->appointment->department ?? 'Not Specified',
            'employment_type' => $this->appointment->employment_type ?? 'Full Time',
            'joining_date' => $this->appointment->joining_date ?? '',
            'reporting_to' => $this->appointment->reporting_to ?? 'HR Manager',
            'work_location' => $this->appointment->work_location ?? '',
            'probation_period' => $this->appointment->probation_period ?? '6 Months',
            'notice_period' => $this->appointment->notice_period ?? '30 Days',
            'ctc_annual' => $this->appointment->annual_ctc ?? ($this->appointment->ctc_annual ?? '0'),
            'ctc_words' => $this->appointment->ctc_words ?? '',
            'basic_monthly' => $this->appointment->basic_monthly ?? '0',
            'basic_annual' => $this->appointment->basic_annual ?? '0',
            'hra_monthly' => $this->appointment->hra_monthly ?? '0',
            'hra_annual' => $this->appointment->hra_annual ?? '0',
            'cca_monthly' => $this->appointment->cca_monthly ?? '0',
            'cca_annual' => $this->appointment->cca_annual ?? '0',
            'special_allowance_monthly' => $this->appointment->special_allowance_monthly ?? '0',
            'special_allowance_annual' => $this->appointment->special_allowance_annual ?? '0',
            'statutory_bonus_monthly' => $this->appointment->statutory_bonus_monthly ?? '0',
            'statutory_bonus_annual' => $this->appointment->statutory_bonus_annual ?? '0',
            'training_allowance_monthly' => $this->appointment->training_allowance_monthly ?? '0',
            'training_allowance_annual' => $this->appointment->training_allowance_annual ?? '0',
            'vpp_monthly' => $this->appointment->vpp_monthly ?? '0',
            'vpp_annual' => $this->appointment->vpp_annual ?? '0',
            'pf_employer_monthly' => $this->appointment->pf_employer_monthly ?? '0',
            'pf_employer_annual' => $this->appointment->pf_employer_annual ?? '0',
            'esi_employer_monthly' => $this->appointment->esi_employer_monthly ?? '0',
            'esi_employer_annual' => $this->appointment->esi_employer_annual ?? '0',
            'gross_monthly' => $this->appointment->gross_monthly ?? '0',
            'gross_annual' => $this->appointment->gross_annual ?? '0',
        ];

        return new Content(
            view: 'emails.offer-letter',
            with: [
                'candidate' => $candidate,
                'companyName' => $this->settings['companyName'] ?? 'TECLA MEDIA',
                'companyEmail' => $this->settings['companyEmail'] ?? '',
                'companyPhone' => $this->settings['companyPhone'] ?? '',
                'hasOfferLetter' => true,
                'offerData' => $offerData,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $firstName = $this->employee->firstname ?? ($this->employee->first_name ?? 'Candidate');
        $filename = 'Offer_Letter_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $firstName) . '.pdf';
        return [
            Attachment::fromData(fn () => $this->pdfBinary, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
