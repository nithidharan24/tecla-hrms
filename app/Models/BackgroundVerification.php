<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackgroundVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'status',
        'documents',
        'remarks',
        'verification_date',
        'verified_by'
    ];

    protected $casts = [
        'documents' => 'array',
        'verification_date' => 'date'
    ];

    // Relationships
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Status constants
    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    // Document types
    const DOCUMENT_TYPES = [
        'aadhaar' => 'Aadhaar Card',
        'pan' => 'PAN Card',
        'passport' => 'Passport',
        'driving_license' => 'Driving License',
        'educational_certificates' => 'Educational Certificates',
        'experience_letters' => 'Experience Letters',
        'address_proof' => 'Address Proof',
        'police_verification' => 'Police Verification',
        'reference_check' => 'Reference Check'
    ];
}