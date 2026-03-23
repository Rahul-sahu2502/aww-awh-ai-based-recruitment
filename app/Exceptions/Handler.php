<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ThrottleRequestsException) {

            // If request is AJAX / JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'alert_message' => 'Too many attempts, please try again after 1 minute.',
                ], 429);
            }

            // Normal form submit
            return redirect()->to($request->url())->with([
                'alert_message' => 'Aapne bahut baar try kiya hai! 1 minute baad try karein.',
                'alert_type' => 'error'
            ]);
        }

        return parent::render($request, $exception);
    }
}
