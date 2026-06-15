<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewPersonalGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'last_year_goal',
        'current_year_goal',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}