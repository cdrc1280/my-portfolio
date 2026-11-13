<?php

use App\Domains\Services\Controllers\Admin\ServiceController;
use App\Domains\Services\Controllers\Public\ServicesController;
use Illuminate\Support\Facades\Route;

Route::get('services', ServicesController::class)->name('services');

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
    // Before the resource, so "settings" is never read as a service id.
    Route::put('services/settings', [ServiceController::class, 'updateSettings'])->name('services.settings');
    Route::resource('services', ServiceController::class)->only(['index', 'store', 'update', 'destroy']);
});
