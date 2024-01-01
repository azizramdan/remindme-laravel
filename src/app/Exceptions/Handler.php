<?php

namespace App\Exceptions;

use App\Enums\CommonError;
use Error;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
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

        $this->renderable(function (Error | Throwable $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
            } else if ($e instanceof ValidationException) {
                $status = $e->status;
            } else if ($e instanceof AuthenticationException) {
                $status = Response::HTTP_UNAUTHORIZED;
            } else {
                $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
            
            switch ($status) {
                case Response::HTTP_BAD_REQUEST:
                case Response::HTTP_UNPROCESSABLE_ENTITY:
                    $response = [
                        'ok' => false,
                        'err' => CommonError::ERR_BAD_REQUEST,
                        'msg' => $e->getMessage(),
                    ];

                    $status = Response::HTTP_BAD_REQUEST;
                    break;
                case Response::HTTP_UNAUTHORIZED:
                    $response = [
                        'ok' => false,
                        'err' => CommonError::ERR_INVALID_ACCESS_TOKEN,
                        'msg' => 'invalid access token',
                    ];
                    break;
                case Response::HTTP_FORBIDDEN:
                    $response = [
                        'ok' => false,
                        'err' => CommonError::ERR_FORBIDDEN_ACCESS,
                        'msg' => "user doesn't have enough authorization",
                    ];
                    break;
                case Response::HTTP_NOT_FOUND:
                    $response = [
                        'ok' => false,
                        'err' => CommonError::ERR_NOT_FOUND,
                        'msg' => 'resource is not found',
                    ];
                    break;
                case Response::HTTP_INTERNAL_SERVER_ERROR:
                    $response = [
                        'status' => $status,
                        'err' => CommonError::ERR_INTERNAL_ERROR,
                        'msg' => config('app.debug') ? $e->getMessage() : 'Server Error',
                    ];
                    break;
                
                default:
                    return null;
                    break;
            }

            return response()->json($response, $status);
        });
    }
}
