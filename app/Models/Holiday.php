<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    // Your table name is 'holidays' (with an 's')
    protected $table = 'holidays';

    protected $fillable = [
        'name_holiday',
        'date_holiday',
        'title',
        'holidaydate',
        'day',
        'branch_id'
    ];

    protected $casts = [
        'date_holiday' => 'date',
        'holidaydate' => 'date'
    ];

    // Helper method to check if a date is a holiday
    public static function isHoliday($date)
    {
        return self::whereDate('holidaydate', $date)
            ->orWhereDate('date_holiday', $date)
            ->exists();
    }
}