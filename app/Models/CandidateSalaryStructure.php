<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateSalaryStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'gross_salary',
        'basic_salary',
        'da_amount',
        'hra_amount',
        'conveyance',
        'special_allowance',
        'medical_allowance',
        'pf_amount',
        'esi_amount',
        'professional_tax',
        'welfare_fund',
        'tds',
        'net_salary',
        'offer_letter_sent',
        'offer_letter_sent_at',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
