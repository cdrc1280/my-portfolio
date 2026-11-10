<?php

use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SkillCategoryController;
use App\Http\Controllers\Admin\SkillController;
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

        Route::post('skills/reorder', [SkillController::class, 'reorder'])->name('skills.reorder');
        Route::resource('skills', SkillController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('skill-categories/reorder', [SkillCategoryController::class, 'reorder'])->name('skill-categories.reorder');
        Route::resource('skill-categories', SkillCategoryController::class)->only(['store', 'update', 'destroy']);

        Route::post('experience/reorder', [ExperienceController::class, 'reorder'])->name('experience.reorder');
        Route::resource('experience', ExperienceController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('education/reorder', [EducationController::class, 'reorder'])->name('education.reorder');
        Route::resource('education', EducationController::class)->only(['store', 'update', 'destroy']);
        Route::post('certificates/reorder', [CertificateController::class, 'reorder'])->name('certificates.reorder');
        Route::resource('certificates', CertificateController::class)->only(['store', 'update', 'destroy']);
    });
