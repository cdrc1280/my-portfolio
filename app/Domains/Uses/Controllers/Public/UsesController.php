<?php

declare(strict_types=1);

namespace App\Domains\Uses\Controllers\Public;

use App\Domains\Uses\Queries\UsesItemQueries;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class UsesController extends Controller
{
    public function __invoke(UsesItemQueries $queries): Response
    {
        return Inertia::render('public/uses', [
            'items' => $queries->ordered(),
        ]);
    }
}
