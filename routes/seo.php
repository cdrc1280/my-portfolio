<?php

use App\Domains\Seo\Controllers\Public\SeoController;
use App\Domains\Seo\Controllers\Public\ShareCardController;
use Illuminate\Support\Facades\Route;

Route::get('sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('robots.txt', [SeoController::class, 'robots'])->name('robots');

// Generated Open Graph images. The specific patterns come before the generic page one.
Route::get('og/projects/{slug}.png', [ShareCardController::class, 'project'])->name('og.project');
Route::get('og/blog/{slug}.png', [ShareCardController::class, 'post'])->name('og.post');
Route::get('og/{page}.png', [ShareCardController::class, 'page'])->name('og.page');
