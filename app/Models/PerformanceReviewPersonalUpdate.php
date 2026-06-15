<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewPersonalUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'married_last_year',
        'married_last_year_details',
        'marriage_plans',
        'marriage_plans_details',
        'studies_last_year',
        'studies_last_year_details',
        'study_plans',
        'study_plans_details',
        'health_issues_last_year',
        'health_issues_last_year_details',
        'certification_plans',
        'certification_plans_details',
        'others_last_year',
        'others_last_year_details',
        'others_current_year',
        'others_current_year_details',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}