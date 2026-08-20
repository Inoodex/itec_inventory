<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
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
        // 1. Never intercept standard Laravel auth, validation, or CSRF token exceptions
        if (
            $e instanceof AuthenticationException ||
            $e instanceof ValidationException ||
            $e instanceof TokenMismatchException
        ) {
            return parent::render($request, $e);
        }

        // 2. Handle Spatie permissions UnauthorizedException as 403
        if ($e instanceof UnauthorizedException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'User does not have the right permissions.'], 403);
            }
            if (view()->exists('errors.403')) {
                return response()->view('errors.403', ['exception' => $e], 403);
            }
            return response()->view('errors.layout', [
                'exception' => $e,
                'title' => 'Access Forbidden - iTech Inventory',
                'code' => '403',
                'badge' => 'Access Denied',
                'heading' => 'Access Restricted',
                'message' => 'You do not have permission to access this page.'
            ], 403);
        }

        // 3. Custom HTTP error views (404, 403, 500, etc.)
        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            if (view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", ['exception' => $e], $status);
            }
        }

        // 4. Render custom 500 error page in production for genuine unhandled server crashes
        if (!config('app.debug') && !$this->isHttpException($e) && !$request->expectsJson()) {
            return response()->view('errors.500', ['exception' => $e], 500);
        }

        return parent::render($request, $e);
    }
}
