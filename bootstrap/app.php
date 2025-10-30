<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
    
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {

            // Only apply to API or JSON requests
            if ($request->expectsJson()) {
                return match (true) {
                    $e instanceof AuthenticationException => response()->json([
                        'success' => false,
                        'message' => 'Unauthorized request.',
                    ], Response::HTTP_UNAUTHORIZED),

                    $e instanceof ValidationException => response()->json([
                        'success' => false,
                        'message' => 'Validation failed.',
                        'errors'  => $e->errors(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY),

                    $e instanceof ModelNotFoundException,
                    $e instanceof NotFoundHttpException => response()->json([
                        'success' => false,
                        'message' => 'Resource not found.',
                        'errors'  => $e->getMessage()
                    ], Response::HTTP_NOT_FOUND),

                    $e instanceof RouteNotFoundException => response()->json([
                        'success' => false,
                        'message' => 'Route not found.',
                    ], Response::HTTP_NOT_FOUND),

                    $e instanceof HttpException => response()->json([
                        'success' => false,
                        'message' => $e->getMessage() ?: 'HTTP error occurred.',
                    ], $e->getStatusCode()),

                    default => response()->json([
                        'success' => false,
                        'message' => 'An unexpected error occurred.',
                        'details' => config('app.debug') ? $e->getMessage() : null,
                    ], Response::HTTP_INTERNAL_SERVER_ERROR),
                };
            }

            // Otherwise, fallback to Laravel's default HTML error pages
            return null;
        });
    })
    ->create();
