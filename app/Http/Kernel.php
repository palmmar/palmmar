<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\InternalApiMiddleware;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // dina ev. andra middleware här...
        'internal.api' => InternalApiMiddleware::class,
    ];
}
