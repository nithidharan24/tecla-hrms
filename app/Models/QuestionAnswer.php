<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionAnswer extends Model
{
    protected $fillable = ['question_id', 'label', 'value', 'color', 'sort_order'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(TemplateQuestion::class, 'question_id');
    }
}
