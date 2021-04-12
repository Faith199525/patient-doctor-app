<?php

namespace App\Exceptions;

use HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Swift_TransportException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel())) ;
            return response()->json(["error"=>"{$model} with the specified id does not exist", "code" => 404], 404);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response("The specified method for the request is invalid", 405);
        }
        if ($exception instanceof NotFoundHttpException) {
            return response("The specified URL does not exist", 404);
        }
        if ($exception instanceof HttpException) {
            return response($exception->getMessage(), $exception->getStatusCode());
        }
        if ($exception instanceof Swift_TransportException) {
            return response($exception->getMessage(), 500);
        }
        if ($exception instanceof IllegalArgumentException) {
            return response()->json(['status' => 'failed', 'message' => $exception->getMessage()], 400);
        }
        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1451) {
                return response("Sorry, you cannot delete this resource permanently because it is related to other resources", 409);
            }
        }
        return parent::render($request, $exception);
    }
}
