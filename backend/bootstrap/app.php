<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias([
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if (! ($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }

            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return \App\Support\ApiResponse::error('Unauthenticated.', 401, []);
            }

            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return \App\Support\ApiResponse::error(
                    $e->getMessage() ?: 'This action is unauthorized.',
                    403,
                    []
                );
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                return \App\Support\ApiResponse::error(
                    $e->getMessage() ?: 'This action is unauthorized.',
                    $e->getStatusCode(),
                    []
                );
            }

            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return \App\Support\ApiResponse::error('The request is invalid.', 422, $e->errors());
            }

            return \App\Support\ApiResponse::error(
                $e->getMessage() ?: 'Something went wrong.',
                method_exists($e, 'getStatus') ? $e->getStatus() : 500,
                []
            );
        });
    })->create();
