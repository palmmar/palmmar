<?php

    use App\Http\Controllers\AuthController;
    use Illuminate\Support\Facades\Route;

// Gäster får se login
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

// Inloggade kan logga ut
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
