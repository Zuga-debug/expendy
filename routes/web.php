<?php

use Illuminate\Support\Facades\Route;

// Fallback route for SPA - Vue handles all frontend routes
Route::get('/{any}', function () {
    return view('app'); // Make sure resources/views/app.blade.php exists
})->where('any', '.*');
