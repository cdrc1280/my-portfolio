<?php

use App\Domains\Testimonials\Controllers\Admin\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('testimonials/reorder', [TestimonialController::class, 'reorder'])->name('testimonials.reorder');
    Route::resource('testimonials', TestimonialController::class)->only(['index', 'store', 'update', 'destroy']);
});
