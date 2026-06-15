<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewProfessionalGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'goal_self',
        'goal_ro',
        'goal_hod',
        'is_last_year',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}