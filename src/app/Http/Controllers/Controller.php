<?php

namespace App\Http\Controllers;

use App\Enums\CommonError;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function success(array|object $data = null, int $status = Response::HTTP_OK)
    {
        $response = [
            'ok' => true,
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return new JsonResponse($response, $status);
    }

    protected function fail(CommonError $error, string $message, int $status = Response::HTTP_BAD_REQUEST)
    {
        return new JsonResponse([
            'ok' => false,
            'err' => $error,
            'msg' => $message,
        ], $status);
    }
}
