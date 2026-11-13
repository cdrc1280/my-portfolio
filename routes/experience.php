<?php

use App\Domains\Experience\Controllers\Admin\CertificateController;
use App\Domains\Experience\Controllers\Admin\EducationController;
use App\Domains\Experience\Controllers\Admin\ExperienceController;
use Illuminate\Support\Facades\Route;

// Shown publicly on About and Home (see profile.php and site.php).
Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('experience/reorder', [ExperienceController::class, 'reorder'])->name('experience.reorder');
    Route::resource('experience', ExperienceController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('education/reorder', [EducationController::class, 'reorder'])->name('education.reorder');
    Route::resource('education', EducationController::class)->only(['store', 'update', 'destroy']);

    Route::post('certificates/reorder', [CertificateController::class, 'reorder'])->name('certificates.reorder');
    Route::resource('certificates', CertificateController::class)->only(['store', 'update', 'destroy']);
});
