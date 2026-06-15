<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'manager_name',
        'opening_time',
        'closing_time',
        'status'
    ];

    protected $casts = [
        'opening_time' => 'datetime:H:i',
        'closing_time' => 'datetime:H:i',
        'status' => 'boolean'
    ];
}