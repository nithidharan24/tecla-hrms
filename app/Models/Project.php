<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'category',
        'status',
        'assigned_to',
        'department',
        'start_date',
        'end_date'
    ];

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(AllEmployee::class, 'assigned_to');
    }
}