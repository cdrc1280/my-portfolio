<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TagController;
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

        Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
        Route::resource('projects', ProjectController::class)->except('show');

        Route::resource('tags', TagController::class)->only(['index', 'store', 'update', 'destroy']);
    });
