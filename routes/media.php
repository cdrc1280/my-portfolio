<?php

use App\Shared\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

/*
| Uploads stored on the `database` disk (MEDIA_DISK=database, used on Vercel), served by path.
*/
Route::get('media/{path}', MediaController::class)->where('path', '.+')->name('media.show');
