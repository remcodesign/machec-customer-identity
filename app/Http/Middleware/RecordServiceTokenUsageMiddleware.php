<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class RecordServiceTokenUsageMiddleware
{
    /**
     * Records usage of the Sanctum token that authenticated this request
     * (D102) — a plain route middleware, since Sanctum ships no "token
     * used" event to hook into cleanly (same reasoning as the web
     * login/logout audit middleware, D93).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        // `exists` excludes a test double such as `Sanctum::actingAs()`'s
        // Mockery-backed token, which is never a persisted row.
        if ($token instanceof PersonalAccessToken && $token->exists) {
            $token->increment('usage_count');
        }

        return $next($request);
    }
}
