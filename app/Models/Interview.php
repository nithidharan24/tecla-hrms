<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'job_id',
        'interviewer_name',
        'interviewer_email',
        'interview_datetime',
        'interview_type',
        'status',
        'notes',
        'rating',
        'feedback'
    ];

    protected $casts = [
        'interview_datetime' => 'datetime',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function job()
    {
        return $this->belongsTo(ManageJob::class, 'job_id');
    }
}