<?php

    use Illuminate\Support\Facades\Route;

    Route::middleware('tenant')->group(function () {
        // Auth-rutter per tenant (login sker på t.ex. acme.palmmar.test)
        require __DIR__.'/auth.php';

        // Skyddade app-rutter
        Route::middleware('auth')->group(function () {
            require __DIR__ . '/users.php';

            Route::get('/', function () {
                return redirect()->route('users.index');
            });
        });
    });
