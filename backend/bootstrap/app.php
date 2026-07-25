<?php

use App\Http\Middleware\EnsureApiVersion;
use App\Http\Middleware\EnsureUserHasPermission;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\LogApiPerformance;
use App\Http\Middleware\LogSlowQueries;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\ValidateUpload;
use App\Support\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')->prefix('api')->group(base_path('routes/api_v2.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'permission' => EnsureUserHasPermission::class,
            'api.version' => EnsureApiVersion::class,
        ]);
        $middleware->append([
            SecurityHeaders::class,
            LogApiPerformance::class,
            LogSlowQueries::class,
            ValidateUpload::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('You do not have permission to access this resource.', [], 403);
            }
        });

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('The given data was invalid.', $exception->errors(), 422);
            }
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Unauthenticated.', [], 401);
            }
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Resource not found.', [], 404);
            }
        });

        $exceptions->render(function (ThrottleRequestsException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Too many requests. Please slow down.', [], 429);
            }
        });

        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Session expired. Please refresh and try again.', [], 419);
            }
        });

        $exceptions->render(function (ServiceUnavailableHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Service temporarily unavailable.', [], 503);
            }
        });

        $exceptions->render(function (HttpException $exception, Request $request) {
            if ($request->is('api/*') && $exception->getStatusCode() < 500) {
                return ApiResponse::error($exception->getMessage() ?: 'Request failed.', [], $exception->getStatusCode());
            }
            if ($request->is('api/*') && $exception->getStatusCode() >= 500) {
                return ApiResponse::error('An unexpected error occurred.', [], 500);
            }
        });
    })->create();
