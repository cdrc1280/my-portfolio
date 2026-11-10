<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Domain\Tags\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Tag
 */
class TagResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'projects_count' => (int) ($this->projects_count ?? 0),
            'posts_count' => (int) ($this->posts_count ?? 0),
        ];
    }
}
