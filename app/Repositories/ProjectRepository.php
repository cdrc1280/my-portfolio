<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository
{
    public function getAllFeatured(): Collection
    {
        return Project::with('category')
            ->where('is_featured', true)
            ->orderBy('order')
            ->get();
    }

    public function getByCategory(string $categorySlug): Collection
    {
        return Project::whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        })->orderBy('order')->get();
    }

    public function findBySlug(string $slug): ?Project
    {
        return Project::with('category')->where('slug', $slug)->first();
    }
}
