<?php

namespace App\Exceptions;

use App\Helper\ApiResponser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    // ... (keep your existing properties)

    public function render($request, Throwable $e)
    {
        // First check if this is an API request (has JSON header or wants JSON)
        if ($this->isApiRequest($request)) {
            return $this->handleApiException($request, $e);
        }

        // Fall back to default Laravel error handling for non-API requests
        return parent::render($request, $e);
    }

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
        });
    }

    protected function isApiRequest($request): bool
    {
        // Check if the request is asking for JSON
        if ($request->wantsJson()) {
            return true;
        }

        // Check if the request has the API prefix (api/)
        if ($request->is('api/*')) {
            return true;
        }

        return false;
    }

    protected function handleApiException($request, Throwable $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof AuthenticationException) {
            return ApiResponser::errorResponse('Unauthenticated', 401);
        }

        if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));
            return ApiResponser::errorResponse(
                "No {$model} found with the specified ID", 
                404
            );
        }

        if ($exception instanceof NotFoundHttpException) {
            return ApiResponser::errorResponse(
                'The specified URL cannot be found',
               404
            );
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return ApiResponser::errorResponse(
                'The specified method for the request is invalid',
                405
            );
        }

        // // Handle any Type of error
        return ApiResponser::errorResponse(
            'An unexpected error occurred. Please try again later.',
            500
        );
    }
}