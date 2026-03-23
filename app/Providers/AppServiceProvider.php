<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        if (config('app.env') == 'production') {
            URL::forceScheme('https');
        }

        RateLimiter::for('user-ip-login', function (Request $request) {

            $userId = $request->username ?? 'guest';

            return Limit::perMinute(3)->by($userId . '|' . $request->ip());
        });

        // ✅ Set global timezone (India Standard Time)
        date_default_timezone_set('Asia/Kolkata');

        // Optional: Carbon locale for formatting (if you use ->diffForHumans() etc.)
        Carbon::setLocale('en_IN');

        ##Rate Limiter for User Routs Attempts
        // ### Candidate form submissions (save details, final submit)
        RateLimiter::for('candidate-forms', function (Request $request) {
            return Limit::perMinute(3) // 3 requests per minute
                ->by($request->user()?->id ?: $request->ip());
        });

        // ### Candidate document uploads
        RateLimiter::for('candidate-documents', function (Request $request) {
            return Limit::perMinute(3) // 3 uploads per minute
                ->by($request->user()?->id ?: $request->ip());
        });

        // ### Admin operations
        RateLimiter::for('admin-operations', function (Request $request) {
            // If you want: you can whitelist some IPs here later
            return Limit::perMinute(10) // 10 operations per minute per admin
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
