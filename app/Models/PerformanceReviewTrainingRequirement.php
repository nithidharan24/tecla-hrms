<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewTrainingRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'training_self',
        'training_ro',
        'training_hod',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}