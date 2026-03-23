<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HSTSMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // HSTS Header (1 year + subdomains + preload-ready)
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains'
        );

        return $response;
    }
}
