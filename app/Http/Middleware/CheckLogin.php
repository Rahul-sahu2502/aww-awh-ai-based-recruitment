<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        $sessRole = Session::get('sess_role');
        $isVerified = Session::get('is_verified', 0);
        $isLoginPage = $request->is('login') || $request->is('add-new-user');
        // $is_password_changed = $request->session()->get('is_password_changed');

        // ## Case 1: Already logged in & accessing login page → redirect to dashboard
        if ($sessRole && $isVerified == 1 && $isLoginPage) {
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

        // ## Case 2: Accessing login/add-new-user while not authenticated → allow
        if ($isLoginPage) {
            return $next($request);
        }

        // ## Case 3: Not logged in and trying to access protected route → redirect to login
        if (!$sessRole || $isVerified != 1) {
            return redirect("/login");
        }

        // ## Case 4: Authenticated and accessing protected route → check role if specified
        if ($role && !in_array($sessRole, explode('-', $role))) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // ## Case 5: Authenticated access to route → allow
        // if ($sessRole == 'Super_admin') {
        //     if ($is_password_changed == 0) {
        //         return redirect('/change-password');
        //     }
        // }

        return $next($request);
    }

}
