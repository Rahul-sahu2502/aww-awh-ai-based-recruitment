<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // Check session-based authentication first
        $sessRole = Session::get('sess_role');
        $isVerified = Session::get('is_verified', 0);

        if ($sessRole && $isVerified == 1) {
            switch ($sessRole) {
                case 'Super_admin':
                    return redirect('admin/admin-dashboard');
                case 'Admin':
                    return redirect('examinor/examinor-dashboard');
                case 'Candidate':
                    return redirect('candidate/candidate-dashboard');
                default:
                    return redirect('/');
            }
        }

        // Fallback to Laravel's built-in Auth system
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
