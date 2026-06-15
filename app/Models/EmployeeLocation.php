<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'latitude', 
        'longitude',
        'address',
        'accuracy'
    ];

    public function checkin()
    {
        return $this->belongsTo(TestCheckin::class, 'employee_id', 'id');
    }
}
