<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCheckin extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'employee_image',
        'device',
        'checkin_time'
    ];

    protected $casts = [
        'checkin_time' => 'datetime'
    ];

    public function location()
    {
        return $this->hasOne(EmployeeLocation::class, 'employee_id', 'id');
    }
}
