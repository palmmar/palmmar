<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $api_key = $request->header('X-Internal-Api-Key');

        if ($api_key !== config('services.internal_api.key')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
