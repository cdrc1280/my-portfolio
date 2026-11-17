<?php

use App\Domains\Board\Controllers\Admin\BoardCardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('board', [BoardCardController::class, 'index'])->name('board.index');
    Route::delete('board/done', [BoardCardController::class, 'clearDone'])->name('board.clear-done');
    Route::post('board/cards/{card}/move', [BoardCardController::class, 'move'])->name('board.cards.move');
    Route::resource('board/cards', BoardCardController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['cards' => 'card'])
        ->names('board.cards');
});
