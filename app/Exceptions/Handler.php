<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            if (view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", ['exception' => $e], $status);
            }
        }

        // Always render custom 500 error page in production or for unhandled server errors
        if (!config('app.debug') && !$this->isHttpException($e) && !$request->expectsJson()) {
            return response()->view('errors.500', ['exception' => $e], 500);
        }

        return parent::render($request, $e);
    }
}
