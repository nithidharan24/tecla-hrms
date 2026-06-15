<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewSpecialInitiative extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'achievement_self',
        'achievement_ro',
        'achievement_hod',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}