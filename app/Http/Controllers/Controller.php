<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

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
}
