<?php

use App\Domains\Blog\Controllers\Admin\PostController;
use App\Domains\Blog\Controllers\Public\BlogController;
use App\Domains\Blog\Controllers\Public\FeedController;
use App\Http\Middleware\EnsureBlogIsEnabled;
use Illuminate\Support\Facades\Route;

// The blog can be switched off in Settings; its public routes then return 404.
Route::middleware(EnsureBlogIsEnabled::class)->group(function () {
    Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
    // Registered before blog/{slug} so "feed.xml" is never read as a slug.
    Route::get('blog/feed.xml', FeedController::class)->name('blog.feed');
    Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
});

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('posts', PostController::class)->except('show');
});
