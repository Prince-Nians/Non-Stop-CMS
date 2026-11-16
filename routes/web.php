<?php

use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Files kept in the shared database (PUBLIC_DISK_DRIVER=database). The CMS serves its own copy so the form
// previews are same-origin (no cross-site permission needed) and do not depend on the backend being awake.
Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*');
