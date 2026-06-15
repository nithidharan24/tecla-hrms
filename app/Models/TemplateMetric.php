<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateMetric extends Model
{
    protected $fillable = ['template_id', 'name', 'description'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(QuestionTemplate::class, 'template_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TemplateQuestion::class, 'metric_id');
    }
}
