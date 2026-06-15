<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllEmployee extends Model
{
    use HasFactory;

    protected $table = 'allemployees';

    public function conversationsAsEmployee()
    {
        return $this->hasMany(Conversation::class, 'employee_id');
    }

    public function conversationsAsHr()
    {
        return $this->hasMany(Conversation::class, 'hr_id');
    }
}
