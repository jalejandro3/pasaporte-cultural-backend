<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function authSuccess(array $data): JsonResponse
    {
        return response()->json($data);
    }

    public function success($data): JsonResponse
    {
        return response()->json(['data' => $data]);
    }

    public function error(string $data, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(['error' => $data], $status);
    }
}
