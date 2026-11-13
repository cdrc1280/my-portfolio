<?php

use App\Domains\Site\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;

/*
| Site: the home page, which brings several modules together.
*/
Route::get('/', HomeController::class)->name('home');
