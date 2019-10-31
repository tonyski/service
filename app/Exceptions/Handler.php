<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Routing\Router;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Modules\Base\Support\Response\ResponseTrait;

class Handler extends ExceptionHandler
{
    use ResponseTrait;

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $e)
    {
        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        } elseif ($e instanceof Responsable) {
            return $e->toResponse($request);
        }

        $e = $this->prepareException($e);

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        } elseif ($e instanceof NotFoundHttpException) {
            return $this->failedWithMessage(__('error.not_found'),$e->getStatusCode());
        }

        return $request->expectsJson()
            ? $this->prepareJsonResponse($request, $e)
            : $this->prepareResponse($request, $e);
    }

    public function prepareJsonResponse($request, Exception $e)
    {
        return $this->failedWithMessageAndErrors(
            $this->convertExceptionToArray($e),
            __($e->getMessage()),
            $this->isHttpException($e) ? $e->getStatusCode() : FoundationResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return $this->failedWithMessageAndErrors($exception->errors(),__('validation.invalid_message'),$exception->status);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->failedWithMessage(__('auth.unauthenticated'),FoundationResponse::HTTP_UNAUTHORIZED);
    }

}
