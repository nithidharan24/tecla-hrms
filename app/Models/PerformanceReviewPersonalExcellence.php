<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewPersonalExcellence extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'personal_attribute',
        'key_indicator',
        'weightage',
        'percentage_self',
        'points_self',
        'percentage_ro',
        'points_ro',
        'percentage_self_total',
        'points_self_total',
        'percentage_ro_total',
        'points_ro_total',
        'total_percentage',
    ];

    protected $casts = [
        'weightage' => 'decimal:2',
        'percentage_self' => 'decimal:2',
        'points_self' => 'decimal:2',
        'percentage_ro' => 'decimal:2',
        'points_ro' => 'decimal:2',
        'percentage_self_total' => 'decimal:2',
        'points_self_total' => 'decimal:2',
        'percentage_ro_total' => 'decimal:2',
        'points_ro_total' => 'decimal:2',
        'total_percentage' => 'decimal:2',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}