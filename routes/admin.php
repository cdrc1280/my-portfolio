<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin CMS routes
|--------------------------------------------------------------------------
| Prefixed with /admin, named admin.*, and gated by `access-admin`. Every
| resource controller additionally authorizes through its domain policy.
*/

Route::middleware(['auth', 'can:access-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
    });
