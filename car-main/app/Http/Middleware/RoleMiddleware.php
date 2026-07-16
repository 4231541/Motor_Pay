<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user() || !$request->user()->hasAnyRole(...$roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have the required role.',
                'errors' => []
            ], 403);
        }

        return $next($request);
    }
}
