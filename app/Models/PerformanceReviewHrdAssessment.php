<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewHrdAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'kra_points_available',
        'kra_points_scored',
        'kra_comment',
        'professional_points_available',
        'professional_points_scored',
        'professional_comment',
        'personal_points_available',
        'personal_points_scored',
        'personal_comment',
        'achievement_points_available',
        'achievement_points_scored',
        'achievement_comment',
        'total_points_available',
        'total_points_scored',
        'total_comment',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}