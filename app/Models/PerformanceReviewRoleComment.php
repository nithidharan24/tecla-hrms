<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewRoleComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'alteration_self',
        'alteration_ro',
        'alteration_hod',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}