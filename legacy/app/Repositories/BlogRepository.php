<?php

namespace App\Repositories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Collection;

class BlogRepository
{
    public function getPublished(): Collection
    {
        return Blog::with('category')
            ->where('status', 'published')
            ->latest('published_at')
            ->get();
    }
}
