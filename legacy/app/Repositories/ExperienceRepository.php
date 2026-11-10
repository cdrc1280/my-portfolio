<?php

namespace App\Repositories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Collection;

class ExperienceRepository
{
    public function getAllOrdered(): Collection
    {
        return Experience::orderBy('order')->get();
    }
}
