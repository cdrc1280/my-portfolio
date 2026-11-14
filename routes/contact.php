<?php

use App\Domains\Contact\Controllers\Admin\MessageController;
use App\Domains\Contact\Controllers\Public\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::patch('messages/read-all', [MessageController::class, 'readAll'])->name('messages.read-all');
    Route::patch('messages/{message}/unread', [MessageController::class, 'unread'])->name('messages.unread');
    Route::resource('messages', MessageController::class)->only(['index', 'show', 'destroy']);
});
