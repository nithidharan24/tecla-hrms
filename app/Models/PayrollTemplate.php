<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollTemplate extends Model
{
    protected $table = 'payroll_templates';

    protected $fillable = [
        'name',
        'content',
    ];

    public $timestamps = true; // ✅ Must be public
}
