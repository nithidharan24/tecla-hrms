<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name', 
        'sub_site_name', 
        'contact_email',
        'contact_phone',
        'contact_address',
        'map',
        'working_hours',
        'currency_name',
        'currency_icon',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
    ];
}