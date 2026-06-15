<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewHodStrengthImprovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'strength',
        'improvement',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}