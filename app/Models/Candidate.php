<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'position_applied',
        'experience_years',
        'expected_salary',
        'resume_path',
        'notes',
        'status'
    ];

    // Add this accessor for the full_name attribute used in the view
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function salaryStructure()
    {
        return $this->hasOne(CandidateSalaryStructure::class);
    }
}