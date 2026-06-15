<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'employee_name',
        'employee_signature',
        'employee_date',
        'ro_name',
        'ro_signature',
        'ro_date',
        'hod_name',
        'hod_signature',
        'hod_date',
        'hrd_name',
        'hrd_signature',
        'hrd_date',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReviewBasicInfo::class, 'review_id');
    }
}