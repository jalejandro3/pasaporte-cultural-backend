<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success($this->userService->getProfile($request->bearerToken()));
    }
}
