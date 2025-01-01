<?php

namespace App\Http\Controllers;

use App\Exceptions\ApplicationException;
use App\Exceptions\InputValidationException;
use App\Services\AuthService as AuthServiceInterface;
use App\Services\TokenService as TokenServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
        private readonly TokenServiceInterface $tokenService,
    )
    {
    }

    /**
     * @throws InputValidationException
     * @throws ApplicationException
     */
    public function login(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success(
            $this->authService->login($request->get('email'), $request->get('password'))
        );
    }

    /**
     * @throws ApplicationException
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $rules = [
            'refresh_token' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->authSuccess($this->tokenService->refresh($request->get('refresh_token')));
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
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
            'repeat_password' => 'required|same:password',
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

    /**
     * @throws InputValidationException|ApplicationException
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'required|email',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->authService->forgotPassword($request->get('email')));
    }

    /**
     * @throws InputValidationException
     * @throws ApplicationException
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $rules = [
            'token' => 'required|string',
            'new_password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
            'repeat_password' => 'required|same:new_password',
        ];

        $messages = [
            'repeat_password.same' => 'The password must match.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->authService->resetPassword($request->get('token'), $request->get('new_password')));
    }

    /**
     * @throws InputValidationException
     * @throws ApplicationException
     */
    public function validateToken(Request $request): JsonResponse
    {
        $rules = [
            'token' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->authService->validateToken($request->get('token')));
    }
}
