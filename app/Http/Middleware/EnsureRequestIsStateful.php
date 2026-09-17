<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRequestIsStateful
{
    /**
     * Reject requests Sanctum did not recognize as coming from a
     * configured stateful domain (`config('sanctum.stateful')`, D8) — no
     * session/cookie middleware ran, so `$request->hasSession()` is false.
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->hasSession(), 403, 'Origin not allowed for a stateful session.');

        return $next($request);
    }
}
