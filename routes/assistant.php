<?php

use App\Domains\Assistant\Controllers\Public\AssistantController;
use Illuminate\Support\Facades\Route;

Route::post('assistant', AssistantController::class)->middleware('throttle:assistant')->name('assistant.ask');
