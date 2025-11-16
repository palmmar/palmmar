<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\InternalApiMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['pong' => true]));

Route::prefix('internal')
    ->middleware(InternalApiMiddleware::class)   // <-- viktigt
    ->group(function () {
        Route::get('/users', [UserController::class, 'getUsers']);
        Route::get('/users/{user_id}', [UserController::class, 'getUser']);
        Route::post('/users', [UserController::class, 'storeUser']);
    });
