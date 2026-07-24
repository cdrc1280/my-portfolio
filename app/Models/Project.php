<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_category_id',
        'title',
        'slug',
        'summary',
        'description',
        'hero_image',
        'gallery',
        'demo_url',
        'github_url',
        'tech_stack',
        'timeline',
        'features',
        'architecture',
        'challenges',
        'solutions',
        'lessons_learned',
        'is_featured',
        'stars_count',
        'order',
    ];

    protected $casts = [
        'gallery' => 'array',
        'tech_stack' => 'array',
        'features' => 'array',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }
}
