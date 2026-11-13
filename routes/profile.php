<?php

use App\Domains\Profile\Controllers\Admin\AvailabilityController;
use App\Domains\Profile\Controllers\Admin\ProfileController;
use App\Domains\Profile\Controllers\Admin\SocialLinkController;
use App\Domains\Profile\Controllers\Public\AboutController;
use App\Domains\Profile\Controllers\Public\ResumeController;
use Illuminate\Support\Facades\Route;

Route::get('about', AboutController::class)->name('about');
Route::get('resume', ResumeController::class)->name('resume.download');

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('availability', AvailabilityController::class)->name('availability.update');

    Route::post('social-links/reorder', [SocialLinkController::class, 'reorder'])->name('social-links.reorder');
    Route::resource('social-links', SocialLinkController::class)->only(['store', 'update', 'destroy']);
});
