<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'subject',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function replacePlaceholders($candidate)
    {
        $placeholders = [
            '{{candidate_name}}' => $candidate->first_name . ' ' . $candidate->last_name,
            '{{first_name}}' => $candidate->first_name,
            '{{last_name}}' => $candidate->last_name,
            '{{email}}' => $candidate->email,
            '{{phone}}' => $candidate->phone,
            '{{position}}' => $candidate->position_applied,
            '{{experience}}' => $candidate->experience_years,
            '{{salary}}' => $candidate->expected_salary ? number_format($candidate->expected_salary) : 'As discussed',
            '{{date}}' => now()->format('F d, Y'),
            '{{company_name}}' => config('app.name', 'Our Company'),
        ];

        $content = $this->content;
        foreach ($placeholders as $placeholder => $value) {
            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }
}