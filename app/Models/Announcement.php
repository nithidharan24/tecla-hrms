<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Announcement extends Model
{
    protected $fillable = ['type', 'icon', 'message', 'posted_by', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    // Icon map for quick selection in the UI
    public static array $typeIcons = [
        'Holiday'     => 'fa-calendar-days',
        'Policy'      => 'fa-file-shield',
        'Birthday'    => 'fa-cake-candles',
        'Anniversary' => 'fa-award',
        'Promotion'   => 'fa-arrow-trend-up',
        'Event'       => 'fa-star',
        'Urgent'      => 'fa-triangle-exclamation',
        'General'     => 'fa-bullhorn',
    ];

    // Active = not yet expired
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    // Set expires_at to 24h from now when not explicitly given
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->expires_at)) {
                $model->expires_at = now()->addHours(24);
            }
            // Auto-fill icon from type map if not set
            if (empty($model->icon) && isset(self::$typeIcons[$model->type])) {
                $model->icon = self::$typeIcons[$model->type];
            }
        });
    }
}
