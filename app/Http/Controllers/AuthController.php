<?php

namespace App\Http\Controllers;

use App\Exceptions\ApplicationException;
use App\Exceptions\InputValidationException;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * @throws ApplicationException
     */
    public function register(Request $request): JsonResponse
    {
        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'id_document' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'repeat_email' => 'required|email|same:email',
            'password' => 'required|string|min:6',
        ];

        $messages = [
            'repeat_email.same' => 'The email must match.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->authService->register($request->all()));
    }
}
