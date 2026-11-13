<?php

use App\Domains\Projects\Controllers\Admin\ProjectController as AdminProjectController;
use App\Domains\Projects\Controllers\Public\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('projects/reorder', [AdminProjectController::class, 'reorder'])->name('projects.reorder');
    Route::resource('projects', AdminProjectController::class)->except('show');
});
