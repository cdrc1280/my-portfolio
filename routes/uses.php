<?php

use App\Domains\Uses\Controllers\Admin\UsesItemController;
use App\Domains\Uses\Controllers\Public\UsesController;
use Illuminate\Support\Facades\Route;

Route::get('uses', UsesController::class)->name('uses');

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('uses/reorder', [UsesItemController::class, 'reorder'])->name('uses.reorder');
    Route::resource('uses', UsesItemController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['uses' => 'uses_item']);
});
