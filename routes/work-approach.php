<?php

use App\Domains\WorkApproach\Controllers\Admin\WorkApproachController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('work-approaches/reorder', [WorkApproachController::class, 'reorder'])->name('work-approaches.reorder');
    Route::resource('work-approaches', WorkApproachController::class)->only(['index', 'store', 'update', 'destroy']);
});
