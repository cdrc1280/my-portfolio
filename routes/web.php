<?php

use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\ResumeController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('resume', ResumeController::class)->name('resume.download');

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
