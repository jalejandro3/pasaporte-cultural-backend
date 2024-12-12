<?php

namespace App\Http\Controllers;

use App\Exceptions\InputValidationException;
use App\Models\User;
use App\Repositories\UserRepository as UserRepositoryInterface;
use App\Services\UserService as UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UserController extends Controller
{
    public function __construct(
        private readonly User $user,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserServiceInterface $userService
    )
    {
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->userRepository->findById($id)) {
            throw new ResourceNotFoundException('User not found.');
        }

        $this->user->destroy($id);

        return $this->success(['message' => 'User deleted successfully.']);
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success($this->userService->getProfile($request->bearerToken()));
    }

    public function updateRole(int $id, Request $request): JsonResponse
    {
        return $this->success($this->userService->updateRole($id, $request->all()));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'email',
            'password' => Password::min(8)->mixedCase()->numbers(),
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->userService->updateProfile($request->bearerToken(), $request->all()));
    }
}
