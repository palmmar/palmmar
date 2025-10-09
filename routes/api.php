<?php

    use Illuminate\Support\Facades\Route;

    // Tom/stub så "require routes/api.php" inte kraschar.
    Route::get('/ping', fn () => response()->json(['pong' => true]));
