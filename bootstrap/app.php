<?php

    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Configuration\Exceptions;
    use Illuminate\Foundation\Configuration\Middleware;

    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            api: __DIR__.'/../routes/api.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
            // Alias för din middleware (så du kan använda 'tenant' i routes)
            $middleware->alias([
                'tenant' => \App\Http\Middleware\IdentifyTenant::class,
            ]);

            // Viktigt: se till att IdentifyTenant körs FÖRE auth/session i web-stacken
            $middleware->prependToGroup('web', \App\Http\Middleware\IdentifyTenant::class);
        })
        ->withExceptions(function (Exceptions $exceptions) {
            // Lämna tomt eller lägg ev. egna renderare/loggning här.
            // Viktigt att detta block FINNS så exception-hanteringen wire:as korrekt.
        })
        ->create();
