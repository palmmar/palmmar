<?php

    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;

    Route::middleware('tenant')->group(function () {
        require __DIR__.'/auth.php';

        Route::middleware('auth')->group(function () {
            require __DIR__.'/users.php';
            Route::get('/', fn () => redirect()->route('users.index'))->name('home');
        });
    });
