<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewGeneralComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'comment_self',
        'comment_ro',
        'comment_hod',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}