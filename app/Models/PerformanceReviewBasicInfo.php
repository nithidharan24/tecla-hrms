<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewBasicInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'employee_id',
        'designation_id',
        'department_id',
        'date_of_join',
        'ro_name',
        'ro_designation',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_name');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}