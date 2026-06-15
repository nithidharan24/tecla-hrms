<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewRoAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'work_issues',
        'work_issues_details',
        'leave_issues',
        'leave_issues_details',
        'stability_issues',
        'stability_issues_details',
        'attitude_issues',
        'attitude_issues_details',
        'other_issues',
        'other_issues_details',
        'overall_performance',
        'overall_performance_details',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}