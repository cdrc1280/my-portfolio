<?php

namespace App\Repositories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Collection;

class SkillRepository
{
    public function getAllOrdered(): Collection
    {
        return Skill::with('category')->orderBy('order')->get();
    }
}
