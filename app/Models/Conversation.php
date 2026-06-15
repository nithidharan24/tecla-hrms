<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'hr_id',
        'branch_id',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(AllEmployee::class, 'employee_id');
    }

    public function hr()
    {
        return $this->belongsTo(AllEmployee::class, 'hr_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
