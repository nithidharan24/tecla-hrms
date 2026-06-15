<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewProfessionalExcellence extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'key_result_area',
        'key_performance_indicator',
        'weightage',
        'percentage_self',
        'points_self',
        'percentage_ro',
        'points_ro',
        'total_percentage_self',
        'total_percentage_ro',
        'total_points_self',
        'total_points_ro',
    ];

    protected $casts = [
        'weightage' => 'decimal:2',
        'percentage_self' => 'decimal:2',
        'points_self' => 'decimal:2',
        'percentage_ro' => 'decimal:2',
        'points_ro' => 'decimal:2',
        'total_percentage_self' => 'decimal:2',
        'total_percentage_ro' => 'decimal:2',
        'total_points_self' => 'decimal:2',
        'total_points_ro' => 'decimal:2',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}