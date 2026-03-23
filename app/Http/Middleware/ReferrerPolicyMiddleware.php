<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ReferrerPolicyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Secure Referrer Policy header
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
