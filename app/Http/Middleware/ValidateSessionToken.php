<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class ValidateSessionToken
{
    /**
     * Handle an incoming request.
     * If session token mismatches DB value for the user, logout and redirect to login with message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $uid = Session::get('uid');
            $sessionToken = Session::get('session_token');

            // If no uid in session, nothing to validate here
            if (empty($uid) || empty($sessionToken)) {
                return $next($request);
            }

            // Fetch token from DB
            $dbToken = DB::table('master_user')->where('ID', $uid)->value('session_token');

            // If DB token is null or mismatch -> force logout
            if ((empty($dbToken) || $dbToken !== $sessionToken) && session()->get('st_login_skip_token') !== true) {
                // Clear session and redirect to login with flash message
                Session::flush();
                // Regenerate session ID
                $request->session()->regenerate();

                // Optionally add a flash message
                // Use Redirect so AJAX requests get a 401 and JSON response? We'll handle both
                if ($request->expectsJson()) {
                    return response()->json(['status' => 'session_invalid', 'message' => 'आपका सत्र समाप्त हो गया है। कृपया पुनः लॉगिन करें।'], 401);
                }

                return Redirect::to('/login')->with('error', 'आपका सत्र समाप्त हो गया है। कृपया पुनः लॉगिन करें।');
            }
        } catch (\Exception $e) {
            // On any unexpected error, allow request to continue (don't block)
            return $next($request);
        }

        return $next($request);
    }
}
