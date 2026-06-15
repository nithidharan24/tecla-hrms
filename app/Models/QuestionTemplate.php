<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'survey_flow',
        'layout',
        'display_image',
        'total_questions',
    ];

    public function metrics(): HasMany
    {
        return $this->hasMany(TemplateMetric::class, 'template_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TemplateQuestion::class, 'template_id')->orderBy('sort_order');
    }

    public function getMetricsCountAttribute()
    {
        return $this->metrics()->count();
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }
}
