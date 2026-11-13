<?php

use App\Domains\Tags\Controllers\Admin\TagController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('tags/reorder', [TagController::class, 'reorder'])->name('tags.reorder');
    Route::resource('tags', TagController::class)->only(['index', 'store', 'update', 'destroy']);
});
