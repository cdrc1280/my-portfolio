<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'position',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'summary',
        'responsibilities',
        'achievements',
        'skills_used',
        'logo',
        'order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'responsibilities' => 'array',
        'achievements' => 'array',
        'skills_used' => 'array',
    ];
}
