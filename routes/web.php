<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';
require __DIR__ . '/users.php';

Route::get('/', function () {
    return redirect()->route('users.index');
})->middleware('auth');
