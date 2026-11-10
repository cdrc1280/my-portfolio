<?php

use App\Http\Controllers\Api\AiChatController;
use App\Http\Controllers\Api\ContactApiController;
use App\Http\Controllers\Api\GithubStatsController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ResumeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/portfolio', [PortfolioController::class, 'index']);
    Route::post('/ai/chat', [AiChatController::class, 'chat']);
    Route::post('/contact', [ContactApiController::class, 'store']);
    Route::get('/github/stats', [GithubStatsController::class, 'stats']);
    Route::get('/github/stats/refresh', [GithubStatsController::class, 'refresh']);
    Route::get('/resume/download', [ResumeController::class, 'download']);
});
