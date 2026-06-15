<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class TemplateQuestion extends Model
{
    protected $fillable = [
        'template_id',
        'metric_id',
        'type',
        'question_text',
        'config',
        'enable_comments',
        'is_mandatory',
        'sort_order',
    ];

    protected $casts = [
        'config' => 'json',
        'enable_comments' => 'boolean',
        'is_mandatory' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(QuestionTemplate::class, 'template_id');
    }

    public function metric(): BelongsTo
    {
        return $this->belongsTo(TemplateMetric::class, 'metric_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id')->orderBy('sort_order');
    }
}
