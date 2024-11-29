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
     * @throws InputValidationException
     * @throws ApplicationException
     */
    public function login(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'bail|required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->authSuccess(
            $this->authService->login($request->get('email'), $request->get('password'))
        );
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
            'password' => 'required|string|min:6',
            'repeat_password' => 'required|string|min:6|same:password',
        ];

        $messages = [
            'repeat_password.same' => 'The password must match.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->authService->register($request->all()));
    }
}
